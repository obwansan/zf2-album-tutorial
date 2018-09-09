<?php

namespace Album\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Album\Model\Album;
 use Album\Form\AlbumForm;

 class AlbumController extends AbstractActionController
 {
     protected $albumTable;

     // With Zend Framework 2, in order to set variables in the view, we
     // return a ViewModel instance where the first parameter of the constructor
     // is an array from the action containing data we need. These are then
     // automatically passed to the view script. The ViewModel object also
     // allows us to change the view script that is used, but the default is
     // to use {controller name}/{action name}.
     public function indexAction()
     {
       // In order to set variables in the view, we return a ViewModel instance
       // where the first parameter of the constructor is an array from the action
       // ('albums') containing data we need. These are then automatically passed to the
       // view script. Seems to pass data to index.phtml
       return new ViewModel(array(
          'albums' => $this->getAlbumTable()->fetchAll(),
       ));
     }

     public function addAction()
     {
        $form = new AlbumForm();
        // get() is in Zend/Form/Fieldset. AlbumForm extends Zend\Form\Form,
        // which extends Zend\Form\Fieldset.
        // Passing 'submit' somehow accesses the submit button from AlbumForm and
        // sets the value to 'Add' (changes it from 'Go'?).
        // We do this here as we’ll want to re-use the form when editing an
        // album and will use a different label.
        $form->get('submit')->setValue('Add');

        // getRequest() is in Zend\Mvc\Controller\AbstractController.
        // AlbumController extends AbstractActionController,
        // which extends AbstractController.
        $request = $this->getRequest();
        // If the Request object’s isPost() method is true, then the form has
        // been submitted and so we set the form’s input filter from an album instance.
        if ($request->isPost()) {
            $album = new Album();
            // setInputFilter() is in Zend\Form\Form. AlbumForm extends Form.
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                // getAlbumTable() returns an AlbumTable class. Then call the
                // AlbumTable class's saveAlbum() method to save the new album row.
                $this->getAlbumTable()->saveAlbum($album);

                // After we have saved the new album row, we redirect back to
                // the list of albums using the Redirect controller plugin.
                return $this->redirect()->toRoute('album');
            }
        }
        // Finally, we return the variables that we want assigned to the view.
        // In this case, just the form object. Note that Zend Framework 2 also
        // allows you to simply return an array containing the variables to be
        // assigned to the view and it will create a ViewModel behind the scenes
        // for you.
        return array('form' => $form);
     }

     public function editAction()
     {
         // params is a controller plugin that provides a convenient way to
         // retrieve parameters from the matched route. We use it to retrieve
         // the id from the route we configured in the modules’ module.config.php.
         // If the id is zero, then we redirect to the add action, otherwise,
         // we continue by getting the album entity from the database.
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'add'
             ));
         }

         // Get the Album with the specified id.  An exception is thrown
         // if it cannot be found, in which case go to the index page.
         try {
             $album = $this->getAlbumTable()->getAlbum($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'index'
             ));
         }

        // The form’s bind() method attaches the model ($album) to the form. This is used in two ways:
        // When displaying the form, the initial values for each element are extracted from the model.
        // After successful validation in isValid(), the data from the form is put back into the model.
        $form  = new AlbumForm();
        // bind() is in Zend\Form\Form. AlbumForm extends Zend\Form\Form.
        $form->bind($album);
        // get() is in Zend/Form/Fieldset. AlbumForm extends Zend\Form\Form,
        // which extends Zend\Form\Fieldset.
        // Get the submit button and set its value to 'Edit'
        $form->get('submit')->setAttribute('value', 'Edit');

        // getRequest() is in Zend\Mvc\Controller\AbstractController. AlbumController
        // extends AbstractActionController, which extends AbstractController.
        // Does it get THE actual HTTP request, or just a sample HTTP request?
        $request = $this->getRequest();
        if ($request->isPost()) {
            // setInputFilter() is in Zend\Form\Form. AlbumForm extends Form.
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
     }

     public function deleteAction()
     {
       $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('album');
         }

         $request = $this->getRequest();
         if ($request->isPost()) {
             // Get post data from the form.
             // <input type="submit" name="del" value="Yes" />
             $del = $request->getPost('del', 'No');

             if ($del == 'Yes') {
                 $id = (int) $request->getPost('id');
                 $this->getAlbumTable()->deleteAlbum($id);
             }

             // Redirect to list of albums
             return $this->redirect()->toRoute('album');
         }
         // If the request is not a POST, then we retrieve the correct database
         // record and assign it to the view, along with the id.
         return array(
             'id'    => $id,
             'album' => $this->getAlbumTable()->getAlbum($id)
         );
     }

     public function getAlbumTable()
     {
        if (!$this->albumTable) {
            // AlbumController extends AbstractActionController which extends
            // AbstractController. getServiceLocator() is in AbstractController.
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
      }
 }
