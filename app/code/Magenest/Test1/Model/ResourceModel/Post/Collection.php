<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test1\Model\ResourceModel\Post;

use Magenest\Test1\Model\Post;
use Magenest\Test1\Model\ResourceModel\Post as PostResourceModel;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection class to get data from post table with join tables
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize collection and set model and resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Post::class, PostResourceModel::class);
    }

    /**
     * Join tables to get post details along with actor and director names
     *
     * @return Select
     */
    public function joinTableToGetActorAndDirectory(): Select
    {
        $actorTable = $this->getTable('magenest_actor');
        $actorMovieTable = $this->getTable('magenest_movie_actor');
        $directorTable = $this->getTable('magenest_director');

        $result = $this
            ->addFieldToSelect('name', 'movie')
            ->addFieldToSelect('description')
            ->addFieldToSelect('rating')
            ->join($directorTable, 'main_table.director_id=' . $directorTable . '.director_id', ['director' => 'name'])
            ->join($actorMovieTable, 'main_table.movie_id=' . $actorMovieTable . '.movie_id', [])
            ->join($actorTable, $actorTable . '.actor_id=' . $actorMovieTable . '.actor_id', ['actor' => 'name']);

        return $result->getSelect();
    }
}
