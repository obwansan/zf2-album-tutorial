<?php

namespace Album\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;

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
       // Seems to return index.phtml
       return new ViewModel(array(
          'albums' => $this->getAlbumTable()->fetchAll(),
       ));
     }

     public function addAction()
     {
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
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
      }
 }
