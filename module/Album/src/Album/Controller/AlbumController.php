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
     }

     public function deleteAction()
     {
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
