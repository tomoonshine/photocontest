<?php
    class vote_custom extends def_module {
		
		function setRating_mod($elem='0') {
			
			if($elem == '0') return;
			$ip=$_SERVER['REMOTE_ADDR'];
			$locking = 3600;
	
			// $this->setElementRating('0','1873');
			// echo $elem;
			// return;
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
			$VoteObj = NULL;
			foreach ($Voters as  $vote)
			{
				
				if ($ip == $vote) {
					// Проверка на блокировку пользователя по времени
					$VoteObj = $objectsCollection->getObject(key($Voters));
					
					// Если ip адресс всё ещё заблокирован то усё на выход иначе запишем эпохольное unixtime и передаём на выставление рейтинга setElementRating
					if ($VoteObj->getValue('vremya_golosovaniya')+$locking > time()) {
						// IP адресс ещё заблокирован
						return;
					}
					//
					$VoteObj->setValue('vremya_golosovaniya', time());
					$this->setElementRating('0',$elem);
				}
				
			}
			
			
			// Добавление объекта в справочник Голосовавшие
			$vote = $objectsCollection->addObject($ip, '146');
			$objectsCollection->getObject($vote)->setValue('ip_adress',$ip);
			$objectsCollection->getObject($vote)->setValue('data_golosovaniya',date('d.m.Y H:i'));
			$objectsCollection->getObject($vote)->setValue('stranica','0123');
			$objectsCollection->getObject($vote)->setValue('vremya_golosovaniya',time());
			
			$this->setElementRating('0', $elem);
		
		}

    };
?>