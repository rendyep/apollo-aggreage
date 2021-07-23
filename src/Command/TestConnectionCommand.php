<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestConnectionCommand extends Command
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('apollo:test-connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $statement = $queryBuilder
            ->select('id', 'channel_order_increment_id', 'status', 'error_log')
            ->from('oms_order')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('status', ':status'),
                    $queryBuilder->expr()->eq('error_log', ':error_log')
                )
            )
            ->setParameters([
                ':status' => 'failed',
                ':error_log' => 'allocation not found',
            ])
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->execute()
        ;

        $results = $statement->fetchAll();
        dump($results);

        return Command::SUCCESS;
    }
}
