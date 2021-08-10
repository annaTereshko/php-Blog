<?php
#***************************************************************************************************#

	function categoryExistanceCheck($catArray, $newCategory){
		
		
				$categoryExists = false;
		
		
				foreach($catArray AS $categories){

					foreach($categories AS $key=>$value){	
					
					
						if($newCategory == $value){
							
							$categoryExists=true;
							
						}
						
						
					}
					
				}
				
				return $categoryExists;
	}
        
        	function videoExistanceCheck($videoPathArray, $newVideoPath){
		
		
				$videoPathExists = false;
		
		
				foreach($videoPathArray AS $videoPath){

					foreach($videoPath AS $key=>$value){	
					
					
						if($newVideoPath == $value){
							
							$videoPathExists=true;
							
						}
						
						
					}
					
				}
				
				return $videoPathExists;
	}



#***************************************************************************************************#


#***************************************************************************************************#



#***************************************************************************************************#


	
#***************************************************************************************************#
?>