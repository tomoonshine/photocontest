<?php
    class vote_custom extends def_module {
		
		function setRating_mod($elem="") {
			
			$ip=$_SERVER['REMOTE_ADDR'];
			// echo "<b>IP Address = $ip</b><br/>";
			
			// // Получение указателя на справочник Голосовавшие
			// $typesCollection = umiObjectTypesCollection::getInstance();
			// $voted = $typesCollection->getType('146');
			// echo "справочник ".$voted->getName()."<br/>";
			// echo "oK<br/>";
			
			$objectsCollection = umiObjectsCollection::getInstance();
			$Voters = $objectsCollection->getGuidedItems('146');
			// var_dump($Voters);
			
			// Проверка на совпадение ip адресса
			foreach ($Voters as  $vote)
			{
				
				if ($ip == $vote) {
					// Проверка на блокировку пользователя по времени
					echo key($Voters);
					echo 'совпадение';
					return;
				}
				
			}
			
			
			// Добавление объекта в справочник Голосовавшие
			// $vote = $objectsCollection->addObject($ip, '146');
			// $objectsCollection->getObject($vote)->setValue('ip_adress',$ip);
			// $objectsCollection->getObject($vote)->setValue('data_golosovaniya',date('d.m.Y H:i'));
			// $objectsCollection->getObject($vote)->setValue('stranica','0123');
			

		}

    };
?>