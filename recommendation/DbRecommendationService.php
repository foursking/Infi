<?php
namespace lestore_recommendation\app\service;

class DbRecommendationService {
    public function getRecommendation($categoryId){
        $recommended_goods_list = get_goods_sidebar_recommandation($cat_id, 10);
        if (empty($recommended_goods_list)) {
            $recommended_goods_list = get_category_goods($cat_id, 0, 10, $type);
            $sql = "REPLACE INTO goods_sidebar_recommandation (goods_id, cat_id, display_order) VALUES ";
            $sql_add = array();
            $recommended_goods_list_total = sizeof($recommended_goods_list);
            foreach ($recommended_goods_list as $rgl_i => $rgl) {
                $sql_add[] = "('{$rgl['goods_id']}', '$cat_id', '" . ($recommended_goods_list_total - $rgl_i) . "')";
            }
            if ($sql_add) {
                $sql .= join(", ", $sql_add);
                $db->query("DELETE FROM goods_sidebar_recommandation WHERE cat_id = '$cat_id' ");
                $db->query($sql);
            }
        }

        return $recommended_goods_list;
    }
}
