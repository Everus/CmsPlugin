<?php

namespace Lakion\CmsPlugin;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class LakionCmsPlugin extends AbstractResourceBundle
{
    /**
     * @var ExtensionInterface|bool
     */
    private $containerExtension;
    /**
     * Returns the plugin's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        if (null === $this->containerExtension) {
            $extension = $this->createContainerExtension();
            if (null !== $extension) {
                if (!$extension instanceof ExtensionInterface) {
                    throw new \LogicException(sprintf('Extension %s must implement %s.', get_class($extension), ExtensionInterface::class));
                }
                // check naming convention for Sylius Plugins
                $basename = preg_replace('/Plugin$/', '', $this->getName());
                $expectedAlias = Container::underscore($basename);
                if ($expectedAlias != $extension->getAlias()) {
                    throw new \LogicException(sprintf(
                        'Users will expect the alias of the default extension of a plugin to be the underscored version of the plugin name ("%s"). You can override "Bundle::getContainerExtension()" if you want to use "%s" or another alias.',
                        $expectedAlias, $extension->getAlias()
                    ));
                }
                $this->containerExtension = $extension;
            } else {
                $this->containerExtension = false;
            }
        }
        if ($this->containerExtension) {
            return $this->containerExtension;
        }
    }

    protected function getContainerExtensionClass()
    {
        $basename = preg_replace('/Plugin$/', '', $this->getName());
        return $this->getNamespace().'\\DependencyInjection\\'.$basename.'Extension';
    }
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM];
    }
}
