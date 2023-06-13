<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Service\Helper;

use Carve\ApiBundle\Service\ApiResourceManager;
use Symfony\Contracts\Service\Attribute\Required;

trait ApiResourceManagerTrait
{
    protected ApiResourceManager $apiResourceManager;

    #[Required]
    public function setApiResourceManager(ApiResourceManager $apiResourceManager)
    {
        $this->apiResourceManager = $apiResourceManager;
    }
}
