<?php
    class vote_custom extends def_module {
		
		
		// Функция добавляет 1 балл в рейтинг страницы и записывает ip адресс 
		// пользователя в справочник "Голосовавшие" который в данной версии имеет номер 146
		// Также проверяется каталог в катором лежит страница в данной версии 147 это тип страницы
		// в которой должно быть поле блокировка на время "blokirovka_polzovatelya_v_sekundah"
		// При следующей попытке проголосовать ip адресс голосующего сравнивается с адресами в базе 
		// если есть то возможность учесть голос будет зависеть от времени на которое блокируються голосующие
		// #page_id - каталог голосования $elem - рейтинговая страница в каталоге
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
			$objectsCollection->getObject($vote)->setValue('stranica',$elem);
			$objectsCollection->getObject($vote)->setValue('vremya_golosovaniya',time());
			
			$this->setElementRating('0', $elem);
			return;
		}
		
		// Функция удаляет количество баллов $bal со страницы $elem
		// Доступна только администратору у которого есть право удалять опросы
		function deleteRating_mod($elem,$bal){
			//rate_sum
			$page = umiHierarchy::getInstance()->getElement($elem);
			$page->setValue('rate_sum',$page->getValue('rate_sum')-$bal);
			echo "Ok";
			return;
		}

    };
?>