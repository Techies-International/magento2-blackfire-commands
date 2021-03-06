<?php

namespace Creatuity\BlackfireCommands\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProfileCategoryCommand
 */
class ProfileCategoryCommand extends Command
{
    public function __construct(
        \Magento\Framework\App\State $state, $name=null
    ) {
        try {
            $state->setAreaCode('frontend');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('blackfire:category')
            ->setDescription('Profiles all category pages');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryFactory = $objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $categoryFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active',1)
            ->setOrder('position', 'ASC');
        foreach ($categories as $category) :
            $output->writeln('<info>Profiling ' . $category->getName() . ' ' . $category->getURL() . '</info>');
            exec('blackfire curl ' . $category->getURL());
        endforeach;
        $output->writeln('<info>Done</info>');
    }
}
