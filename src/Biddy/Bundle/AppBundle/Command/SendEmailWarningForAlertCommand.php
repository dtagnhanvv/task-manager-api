<?php

namespace Biddy\Bundle\AppBundle\Command;

use Swift_Mailer;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\AlertManagerInterface;
use Biddy\Model\Core\AlertInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Repository\Core\AlertRepositoryInterface;
use Biddy\Service\Util\StringUtilTrait;

class SendEmailWarningForAlertCommand extends ContainerAwareCommand
{
    use StringUtilTrait;
    const INPUT_ALERT_TYPES = 'types';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AccountManagerInterface
     */
    private $accountManager;

    /**
     * @var AlertManagerInterface
     */
    private $alertManager;

    /**
     * @var AlertRepositoryInterface
     */
    private $alertRepository;

    /**
     * @var Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('biddy:alert:email-warning:send')
            ->addOption(self::INPUT_ALERT_TYPES, 't', InputOption::VALUE_OPTIONAL,
                'alert types to be send to user, warning, actionRequired...')
            ->setDescription('Send email alert for critical alerts for Account');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();
        $this->logger = $container->get('logger');

        $this->accountManager = $container->get('biddy_user.domain_manager.account');
        $this->alertManager = $container->get('biddy.domain_manager.alert');
        $this->alertRepository = $container->get('biddy.repository.alert');
        $this->swiftMailer = $container->get('swiftmailer.mailer');

        $accounts = $this->accountManager->allActiveAccounts();
        $output->writeln(sprintf('sending alert Emails for %d accounts', count($accounts)));

        // send Email Alert
        $alertTypes = $this->getAlertTypes($input);
        $emailAlertsCount = $this->sendEmailAlertForAccount($output, $accounts, $alertTypes);

        $output->writeln(sprintf('command run successfully: emails sent to %d Account.', $emailAlertsCount));
    }

    /**
     * send Email Alert for all Accounts
     *
     * @param OutputInterface $output
     * @param array|AccountInterface[] $accounts
     * @param $alertTypes
     * @return int migrated update alert type count
     */
    private function sendEmailAlertForAccount(OutputInterface $output, array $accounts, $alertTypes)
    {
        $emailAlertsCount = 0;

        foreach ($accounts as $account) {
            if (!$account instanceof AccountInterface) {
                continue;
            }

            $emails = $account->getEmailSendAlert();
            if (!is_array($emails) || empty($emails)) {
                $output->writeln(sprintf('[warning] Account %s (id:%d) missing email to send alert. Please set email for this account then run again.', $this->getAccountName($account), $account->getId()));
                continue;
            }

            $alerts = $this->alertRepository->getAlertsToSendEmailByTypesQuery($account, $alertTypes);
            if (!is_array($alerts)) {
                continue;
            }

            foreach ($emails as $email) {
                if (!is_array($email) || !array_key_exists('email', $email)) {
                    continue;
                }

                $sentEmailsNumber = $this->sendEmailAlert($output, $account, $alerts, $email['email']);
                if ($sentEmailsNumber > 0) {
                    $emailAlertsCount++;
                }
            }
        }

        return $emailAlertsCount;
    }

    /**
     * send email alert to an email
     *
     * @param OutputInterface $output
     * @param AccountInterface $account
     * @param array $alerts
     * @param string $email
     * @return int number of sent emails
     */
    private function sendEmailAlert(OutputInterface $output, AccountInterface $account, array $alerts, $email)
    {
        if (empty($alerts)) {
            return 0;
        }

        // create email, rendering from a template:
        // src/Biddy/Bundle/AppBundle/Resources/views/Alert/email.html.twig
        $sender = $this->getContainer()->getParameter('mailer_sender');
        $emailSubject = '[Biddy] Email notify alert on Biddy System';
        $accountName = $this->getAccountName($account);
        $alertDetails = $this->getAlertDetails($alerts);

        $newEmailMessage = (new \Swift_Message())
            ->setFrom($sender)
            ->setTo($email)
            ->setSubject($emailSubject)
            ->setBody(
                $this->getContainer()->get('templating')->render(
                    'BiddyAppBundle:Alert:email.html.twig',
                    ['account_name' => $accountName, 'alert_details' => $alertDetails]
                ),
                'text/html');

        // send email
        $sendSuccessNumber = $this->swiftMailer->send($newEmailMessage);

        // update isSent to true for all alerts have been sent email
        if ($sendSuccessNumber > 0) {
            $output->writeln(sprintf('sent %d emails to Account %s (id:%d).', $sendSuccessNumber, $accountName, $account->getId()));

            foreach ($alerts as $alert) {
                if (!$alert instanceof AlertInterface) {
                    continue;
                }

                $alert->setIsSent(true);
                $this->alertManager->save($alert);
            }
        }

        return $sendSuccessNumber;
    }

    /**
     * get Account Name
     *
     * @param AccountInterface $account
     * @return string
     */
    private function getAccountName(AccountInterface $account)
    {
        return $account->getFirstName() . ' ' . $account->getLastName();
    }

    /**
     * get Alert Details
     *
     * @param array $alerts
     * @return array
     */
    private function getAlertDetails(array $alerts)
    {
        $alertDetails = [];

        foreach ($alerts as $alert) {
            if (!$alert instanceof AlertInterface) {
                continue;
            }

            $alertDetails[] = $this->fillEmailBody($alert);
        }

        return array_unique($alertDetails);
    }

    /**
     * @param AlertInterface $alert
     * @return string
     */
    private function fillEmailBody(AlertInterface $alert)
    {
        switch ($alert->getCode()) {
            //TODO
            default:
                return 'Unknown alert code . Please contact your account manager';
        }
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    private function getAlertTypes(InputInterface $input)
    {
        $alertString = $input->getOption(self::INPUT_ALERT_TYPES);
        $alertTypesByComma = explode(",", $alertString);
        $alertTypesBySemicolon = explode(";", $alertString);

        $alertTypes = array_merge($alertTypesByComma, $alertTypesBySemicolon);
        $alertTypes = array_map(function ($alertType) {
            //Remove space
            return trim($alertType);
        }, $alertTypes);

        $alertTypes = array_filter($alertTypes, function ($alertType) {
            return in_array($alertType, AlertInterface::SUPPORT_ALERT_TYPES);
        });

        if (empty($alertTypes)) {
            //For old behavior, use for cron job missing input alert types
            $alertTypes[] = AlertInterface::ALERT_TYPE_WARNING;
        }

        return $alertTypes;
    }
}