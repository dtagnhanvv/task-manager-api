<?php

namespace Biddy\Bundle\UserSystem\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Rollerworks\Bundle\MultiUserBundle\DependencyInjection\Compiler\RegisterFosUserMappingsPass;

class BiddyUserSystemAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(RegisterFosUserMappingsPass::createOrmMappingDriver('biddy_user_system_admin'));
    }
}
