<?php

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class AlbumTable
{
   protected $tableGateway;

   public function __construct(TableGateway $tableGateway)
   {
       $this->tableGateway = $tableGateway;
   }
   // Retrieves all albums rows from the database (table) as a ResultSet
   public function fetchAll()
   {
       $resultSet = $this->tableGateway->select();
       return $resultSet;
   }
   //  Retrieves a single row (from the table) as an Album object
   public function getAlbum($id)
   {
       $id  = (int) $id;
       // Get set of rows with the $id passed in (var_dump it!)
       $rowset = $this->tableGateway->select(array('id' => $id));
       // Get the first row with the $id
       $row = $rowset->current();
       if (!$row) {
           throw new \Exception("Could not find row $id");
       }
       return $row;
   }
   //  Either creates a new row in the database or updates a row that already exists
   public function saveAlbum(Album $album)
   {
       $data = array(
           'artist' => $album->artist,
           'title'  => $album->title,
       );

       $id = (int) $album->id;
       if ($id == 0) {
           // Create a new row in the database
           $this->tableGateway->insert($data);
       } else {
           if ($this->getAlbum($id)) {
               // Update a row that already exists
               $this->tableGateway->update($data, array('id' => $id));
           } else {
               throw new \Exception('Album id does not exist');
           }
       }
   }
   // Removes the row completely
   public function deleteAlbum($id)
   {
       $this->tableGateway->delete(array('id' => (int) $id));
   }
}
