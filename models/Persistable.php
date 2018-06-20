<?php

namespace BWB\Framework\mvc;

interface Persistable {
   public function Create();
   public function Retrieve();
   public function Update();
   public function Delete();
   
}
