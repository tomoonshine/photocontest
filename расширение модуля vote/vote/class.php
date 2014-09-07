<?php
    class vote_custom extends def_module {
		
		function setRating_mod($page_id="0", $elem='0') {
			
			if($page_id == '0') return;
			if($elem == '0') return;
			$ip=$_SERVER['REMOTE_ADDR'];
			
			$page = umiHierarchy::getInstance()->getElement($page_id);
			// Если каталог фотоконкурса не являеться фотоконкурсом то выход
			if($page->getObjectTypeId() != 147) return;
			
			$locking = $page->getValue('blokirovka_polzovatelya_v_sekundah');
			
	
			$objectsCollection = umiObjectsCollection::getInstance();
			$Voters = $objectsCollection->getGuidedItems('146');
			
				
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
					return;
				}
				
			}
			
			
			// Добавление объекта в справочник Голосовавшие
			$vote = $objectsCollection->addObject($ip, '146');
			$objectsCollection->getObject($vote)->setValue('ip_adress',$ip);
			$objectsCollection->getObject($vote)->setValue('data_golosovaniya',date('d.m.Y H:i'));
			$objectsCollection->getObject($vote)->setValue('stranica','0123');
			$objectsCollection->getObject($vote)->setValue('vremya_golosovaniya',time());
			
			$this->setElementRating('0', $elem);
			return;
		}

    };
?>