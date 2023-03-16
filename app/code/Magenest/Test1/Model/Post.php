<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test1\Model;

use Magento\Framework\Model\AbstractModel;

/**
 *  Model
 */
class Post extends AbstractModel
{
    public const MOVIE_NAME = 'name';

    /**
     * Model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Post::class);
    }

    /**
     * Get name
     *
     * @return array|mixed|null
     */
    public function getName(): mixed
    {
        return $this->getData(self::MOVIE_NAME);
    }
}
