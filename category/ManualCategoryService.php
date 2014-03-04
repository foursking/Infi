<?php
namespace esmeralda\category;

class ManualCategoryService extends DbCategoryService{

    public function __construct($dao){
        parent::__construct($dao); 
    }

    public function getNl($language){
        $nl = parent::getNl($language);
        $name = $this->getName('page_common_weekly_deal', $language);
        $nl['999999'] = array('name' => $name,
                              'url'  => "wholesale-weekly-deal/");
        $name = $this->getName('page_common_plus_size_wedding_dresses', $language);
        $nl['q1'] = array('name'     => $name,
                              'url'  => "search.php?q=" . urlencode('+' . $name));
        $name = $this->getName('page_common_beach_wedding_dresses', $language);
        $nl['q2'] = array('name'     => $name, 
                              'url'  => "search.php?q=" . urlencode('+' . $name));
        $name = $this->getName('page_common_garden_wedding_dresses', $language);
        $nl['q3'] = array('name'     => $name,
                              'url'  => "search.php?q=" . urlencode('+' . $name));
        $name = $this->getName('page_common_graduation_dresses', $language);
        $nl['q4'] = array('name'     => $name,
                              'url'  => "search.php?q=" . urlencode('+' . $name));
        return $nl;
    }

    private function getName($nlId, $language){
        return $this->dao->getNl($nlId, $language);
    }
	
	protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $rs = parent::buildTreeNodes($map, $childRelation, $parentRelation, $id);
        $id = 'Manual';
        $this->modifyCategory($rs, '999999', '1', '', $map, $childRelation, $parentRelation);
		$this->modifyCategory($rs, 'q1', '2', '', $map, $childRelation, $parentRelation);
		$this->modifyCategory($rs, 'q2', '2', '', $map, $childRelation, $parentRelation);
		$this->modifyCategory($rs, 'q3', '2', '', $map, $childRelation, $parentRelation);
	
		$this->modifyCategory($rs, 'q4', '3', '', $map, $childRelation, $parentRelation);

        $this->modifyCategoryTree($rs, $childRelation, $parentRelation);
    }

	private function modifyCategory(&$rs, $id, $parentId, $url, &$map, &$childRelation, &$parentRelation){
        $map[$id] = parent::createCategory($id, array('url'=>$url));
        $childRelation[$parentId][] = $id;
        $parentRelation[$id][] = $parentId;
	}
	
	CONST WEDDING_SHOES_ID = '47';
	CONST WEDDING_ACCESSORY_ID = '5';
	//CONST SPECIAL_OCCASION_DRESSES_ID = '3';
	private function modifyCategoryTree(&$rs, &$childRelation, &$parentRelation){
        $childRelation[ManualCategoryService::WEDDING_ACCESSORY_ID][] = ManualCategoryService::WEDDING_SHOES_ID;
        $parentRelation[ManualCategoryService::WEDDING_SHOES_ID][] = ManualCategoryService::WEDDING_ACCESSORY_ID;

		//$childRelation[ManualCategoryService::SPECIAL_OCCASION_DRESSES_ID] = array();
	}
}
