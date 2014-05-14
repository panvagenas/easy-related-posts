<?php

/**
 * Easy related posts .
 *
 * @package Easy_Related_Posts_Related
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link http://example.com
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

/**
 * Related class.
 *
 * @package Easy_Related_Posts_Related
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpProRelated {

    /**
     * Relative data obj
     *
     * @since 1.0.0
     * @var erpRelData
     */
    private $relData;

    /**
     * Pool of reldata objects
     *
     * @since 1.0.0
     * @var array
     */
    private $relDataPool = array();

    /**
     * Options array.
     * All critical must be set
     *
     * @since 1.0.0
     * @var erpOptions
     */
    private $options = array();

    /**
     * Instance of this class.
     *
     * @since 1.0.0
     * @var erpProRelated
     */
    protected static $instance = null;
    
    /**
     * Deafult query limit when rating posts
     * @var int Default 100
     */
    private $queryLimit = 500;

    /**
     * Return an instance of this class.
     *
     * @since 1.0.0
     * @return erpProRelated A single instance of this class.
     */
    public static function get_instance(&$options) {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self($options);
        }
        self::$instance->options = $options;
        return self::$instance;
    }

    /**
     *
     * @param array $options
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since 1.0.0
     */
    protected function __construct($options) {
        if (!class_exists('erpQueryFormater')) {
            erpPaths::requireOnce(erpPaths::$erpQueryFormater);
        }
        if (!class_exists('erpRatingSystem')) {
            erpPaths::requireOnce(erpPaths::$erpRatingSystem);
        }
        if (!class_exists('erpRelData')) {
            erpPaths::requireOnce(erpPaths::$erpRelData);
        }
        $this->options = $options;
    }

    public function getRelated($pid) {
        /**
         * Check if we have a reldata obj with same query and if yes return it
         */
        foreach ($this->relDataPool as $key => $value) {
            $missMatch = $value->criticalOptionsMismatch($this->options->getOptions());
            if (empty($missMatch)) {
                $this->relData = $value;
                return $this->relData->getResult();
            }
        }
        // Check if we have relTable in pool
        foreach ($this->relDataPool as $key => $value) {
            if ($value->pid == $pid) {
                $relTable = $value->relTable;
                break;
            }
        }

        $criticalOptions = array_intersect_key($this->options->getOptions(), array_flip(erpDefaults::$criticalOpts));
        $this->relData = new erpRelData($pid, $criticalOptions, $relTable);
        /**
         * If no cached ratings or not the required number of posts
         */
        if (empty($relTable) || count($relTable) < $this->options->getNumberOfPostsToDiplay()) {
            $relTable = $this->doRating($pid);
        }

        /**
         * If reltable is still empty return an empty wp_query obj
         */
        if (empty($relTable)) {
            // Normally this should return an empty wp_query
            return $this->relData->getResult();
        }

        $this->relData->setRelTable($relTable);
        $ratingSystem = erpRatingSystem::get_instance($this->relData);
        $weights = $this->calcWeights();
        $ratingSystem->setWeights($weights);
        $ratingSystem->formRatingsArrays();
        $ratingSystem->sortRatingsArrays($this->options->getSortRelatedBy(true));
        $slicedArray = $ratingSystem->getSlicedRatingsArrayFlat($this->options->getOffset(), $this->options->getNumberOfPostsToDiplay());

        $qForm = new erpQueryFormater();
        $qForm->setMainArgs($pid);
        $qForm->exPostTypes($this->options->getValue('postTypes'));
        $qForm->exCategories($this->options->getValue('categories'));
        $qForm->exTags($this->options->getValue('tags'));
        $qForm->setPostInArg(array_keys($slicedArray));

        $this->relData->setWP_Query($qForm->getArgsArray(), $this->options->getNumberOfPostsToDiplay(), $this->options->getOffset());
        $this->relData->getResult();
        $this->relData->setRatings($slicedArray);
        $this->relData->sortWPQuery(array_keys($slicedArray));
        array_push($this->relDataPool, $this->relData);

        return $this->relData->getResult();
    }

    public function doRating($pid) {
        $qForm = new erpQueryFormater();
        // Make sure relData is populated, this can happen when do rating
        // is called outside of $this->getRelated
        if($this->relData == null){
            $this->relData = new erpRelData($pid, erpDefaults::$criticalOpts);
        }
        $ratingSystem = erpRatingSystem::get_instance($this->relData);

        // Check if a query limit is set.
        $qLimit = (isset($queryLimit) && is_numeric($queryLimit)) ? $queryLimit : $this->queryLimit;
        
        $qForm->setMainArgs($pid);
        
        $postCats = get_the_category($pid);
        $postTags = get_the_tags($pid);
        $relTable = array();
        if (!empty($postCats)) {
            $qForm->clearTags()
                    ->clearPostInParam()
                    ->clearPostTypes()
                    ->setCategories($postCats);

            $qForm->exPostTypes($this->options->getValue('postTypes'))
                    ->exCategories($this->options->getValue('categories'))
                    ->exTags($this->options->getValue('tags'));
            $wpq = $this
                    ->relData
                    ->setQueryLimit($queryLimit, 0)
                    ->setWP_Query($qForm->getArgsArray(), $qLimit, 0)
                    ->getResult();
            $postsArray = $wpq->posts;
            if (!empty($postsArray)) {
                foreach ($postsArray as $key => $value) {
                    $relTable [$value->ID] ['score2_cats'] = $ratingSystem->rateBasedOnCats($pid, $value->ID);
                    $relTable [$value->ID] ['score1_cats'] = $ratingSystem->rateBasedOnCats($value->ID, $pid);
                    $relTable [$value->ID] ['score2_tags'] = $ratingSystem->rateBasedOnTags($pid, $value->ID);
                    $relTable [$value->ID] ['score1_tags'] = $ratingSystem->rateBasedOnTags($value->ID, $pid);
                    $relTable [$value->ID] ['post_date1'] = get_the_time('Y-m-d', $pid);
                    $relTable [$value->ID] ['post_date2'] = get_the_time('Y-m-d', $value->ID);

                    $relTable [$value->ID] ['pid1'] = $pid;
                    $relTable [$value->ID] ['pid2'] = $value->ID;
                }
            }
        }
        if (!empty($postTags)) {
            $qForm->clearCategories()
                    ->clearPostInParam()
                    ->clearPostTypes()
                    ->setTags($postTags);
            $qForm->exPostTypes($this->options->getValue('postTypes'))
                    ->exCategories($this->options->getValue('categories'))
                    ->exTags($this->options->getValue('tags'));
            $wpq = $this
                    ->relData
                    ->setQueryLimit($queryLimit, 0)
                    ->setWP_Query($qForm->getArgsArray(), $qLimit, 0)
                    ->getResult();
            $postsArray = $wpq->posts;
            if (!empty($postsArray)) {
                $inserted = array_keys($relTable);
                foreach ($postsArray as $key => $value) {
                    if (!in_array($value->ID, $inserted)) {
                        $relTable [$value->ID] ['score2_cats'] = $ratingSystem->rateBasedOnCats($pid, $value->ID);
                        $relTable [$value->ID] ['score1_cats'] = $ratingSystem->rateBasedOnCats($value->ID, $pid);
                        $relTable [$value->ID] ['score2_tags'] = $ratingSystem->rateBasedOnTags($pid, $value->ID);
                        $relTable [$value->ID] ['score1_tags'] = $ratingSystem->rateBasedOnTags($value->ID, $pid);
                        $relTable [$value->ID] ['post_date1'] = get_the_time('Y-m-d H:i:s', $pid);
                        $relTable [$value->ID] ['post_date2'] = get_the_time('Y-m-d H:i:s', $value->ID);

                        $relTable [$value->ID] ['pid1'] = $pid;
                        $relTable [$value->ID] ['pid2'] = $value->ID;
                    }
                }
            }
        }

        wp_reset_postdata();
        return $relTable;
    }

    /**
     * Returns the best rated posts for all options defined 
     * in erpDefaults::$fetchByOptionsWeights, 
     * erpDefaults::$sortRelatedByOption
     *
     * @param int $pid
     * @param int $relTable
     * @return array Array  with post ids of the best
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since 1.0.0
     */
    private function chooseTheBest($pid, $relTable) {
        /**
         * CHECK How we should set the best count?
         * Maybe search for the biggest value in all
         * options...
         */
        $bestCount = 15;

        $relData = new erpRelData($pid, erpDefaults::$criticalOpts, $relTable);

        $ratingSystem = erpRatingSystem::get_instance($relData);

        $ratingsFlatenedPool = array();

        /**
         * Get the best for all sorting options
         */
        foreach (erpDefaults::$fetchByOptionsWeights as $option => $weights) {
            $ratingSystem->setWeights($weights);
            $ratingSystem->formRatingsArrays();
            foreach (erpDefaults::$sortRelatedByOption as $key => $value) {
                $ratingSystem->sortRatingsArrays($value);
                foreach (array_keys($ratingSystem->getSlicedRatingsArrayFlatLoose($bestCount)) as $k => $v) {
                    if (!in_array($v, $ratingsFlatenedPool)) {
                        array_push($ratingsFlatenedPool, $v);
                    }
                }
            }
        }
        /**
         * return the result
         */
        return $ratingsFlatenedPool;
    }

    /**
     * Calculates weights based on options
     *
     * @return array Assoc array (categories,tags,clicks)
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since 1.0.0
     */
    private function calcWeights() {
        return isset(erpDefaults::$fetchByOptionsWeights[$this->options->getFetchBy()]) ? erpDefaults::$fetchByOptionsWeights[$this->options->getFetchBy()] : erpDefaults::$fetchByOptionsWeights['categories'];
    }

    public function isInPool($pid) {
        foreach ($this->relDataPool as $k => $v) {
            if ($v->pid == $pid) {
                return $k;
            }
        }
        return false;
    }

    public function getRatingsFromRelDataObj() {
        return $this->relData->getRatings();
    }

}
