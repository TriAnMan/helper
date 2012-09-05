<?php
/**
 * Класс обеспечивает хелпер-методы для сниппетов данного пакета
 */

class Helper {
	/**
	 * Метод отдает ближайший сестринский элемент. В случае если
	 * $isCategory == false отдает и "двоюродные" сестринские элементы.
	 * @global object $modx
	 * @param boolean $next отдавать следующий элемент или предыдущий
	 * @param boolean $isCategory выбирать узлы или листья дерева.
	 * @return array массив из id и pagetitle нужного ресурса
	 */
	static function getFirstSibling($next = false, $isCategory = false) {
		global $modx;

		$sPageTitle = $modx->resource->get('pagetitle');
		$iParentId = $modx->resource->get('parent');

		$finalQuery = $modx->newQuery('modResource');
		$finalQuery->select('`modResource`.`id`, `modResource`.`pagetitle`');
		$finalQuery->sortby('pagetitle', $next ? 'ASC' : 'DESC');
		$whereClause = array(
			'isfolder' => $isCategory,
			'pagetitle:'. ($next ? '>' : '<') => $sPageTitle,
		);
		if(!$isCategory){
			// Ищем прародителя данной странички
			$query = $modx->newQuery('modResource');
			$query->select('`modResource`.`parent`');
			$query->where(array('id' => $iParentId));
			$rows = xPDOObject::_loadRows($modx, 'modResource', $query);

			$iParentId = $rows->fetchColumn();

			// Выбираем всех детей данного прародителя
			$query = $modx->newQuery('modResource');
			$query->select('`modResource`.`id`');
			$query->where(array('parent' => $iParentId, 'isfolder' => 1));
			$rows = xPDOObject::_loadRows($modx, 'modResource', $query);

			$result = $rows->fetchAll(PDO::FETCH_COLUMN);

			// Выбираем всех детей принадлежащих списку категорий
			$whereClause['parent:IN'] = $result;
		} else {
			// Иначе выбираем детей принадлежащих только одной категории
			$whereClause['parent'] = $iParentId;
		}
		$finalQuery->where($whereClause);
		$finalQuery->limit(1);


    	$rows = xPDOObject::_loadRows($modx, 'modResource', $finalQuery);

		$result = $rows->fetch();
		return $result;
	}

	static function getSiblingList($isCategory = true) {
		global $modx;

		$iParentId = $modx->resource->get('parent');

		$finalQuery = $modx->newQuery('modResource');
		$finalQuery->select('`modResource`.`id`, `modResource`.`pagetitle`');
		$finalQuery->sortby('pagetitle', 'ASC');
		$whereClause = array(
			'isfolder' => $isCategory,
			'parent' => $iParentId,
		);
		$finalQuery->where($whereClause);

    	$rows = xPDOObject::_loadRows($modx, 'modResource', $finalQuery);
		$result = $rows->fetchAll();

		return $result;
	}
}