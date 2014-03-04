<?php
namespace lestore_recommendation\app\service;

interface RecommendationService {
	const ID = '__RECOMMENDATION_SERVICE__';

    public function getRecommendation($categoryId);
}

