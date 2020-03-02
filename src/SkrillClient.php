<?php

declare(strict_types=1);

namespace Skrill;

use ArrayObject;
use LimitIterator;
use SplFileObject;
use DateTimeImmutable;
use DateTimeInterface;
use Skrill\ValueObject\Sid;
use Skrill\ValueObject\Url;
use Skrill\ValueObject\Email;
use Skrill\Response\Response;
use Skrill\Factory\SidFactory;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\ClientInterface;
use Skrill\Request\SaleRequest;
use Skrill\Response\HistoryItem;
use Skrill\ValueObject\Password;
use Skrill\Request\PayoutRequest;
use Skrill\Request\RefundRequest;
use Skrill\Request\TransferRequest;
use Skrill\Factory\ResponseFactory;
use Skrill\Request\OnDemandRequest;
use Skrill\ValueObject\CompanyName;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\SkrillResponseException;

/**
 * Skrill HTTP client.
 */
final class SkrillClient implements
    SkrillHistoryClientInterface,
    SkrillOnDemandClientInterface,
    SkrillSaleClientInterface,
    SkrillTransferClientInterface,
    SkrillRefundClientInterface,
    SkrillPayoutClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * A description to be shown on the Skrill payment page in the logo area if there is no logo_url parameter.
     *
     * recipient_description
     *
     * @var CompanyName|null
     */
    private $companyName;

    /**
     * Email address of your Skrill merchant account.
     *
     * pay_to_email
     *
     * @var Email
     */
    private $merchantEmail;

    /**
     * The URL of the logo which you would like to appear in the top right of the Skrill page.
     *
     * logo_url
     *
     * @var Url|null
     */
    private $logoUrl;

    /**
     * Skrill API/MQI password.
     *
     * @var string
     */
    private $password;

    /**
     * @param ClientInterface  $client
     * @param Email            $merchantEmail
     * @param Password         $password
     * @param Url|null         $logoUrl
     * @param CompanyName|null $companyName
     */
    public function __construct(
        ClientInterface $client,
        Email $merchantEmail,
        Password $password,
        Url $logoUrl = null,
        CompanyName $companyName = null
    ) {
        $this->client = $client;
        $this->companyName = $companyName;
        $this->merchantEmail = $merchantEmail;
        $this->logoUrl = $logoUrl;
        $this->password = md5(strval($password));
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function prepareSale(SaleRequest $request): Sid
    {
        $params = $request->getPayload();
        $params['prepare_only'] = 1; // Forces only the SID to be returned without the actual page.
        $params['pay_to_email'] = strval($this->merchantEmail);

        if (null != $this->logoUrl) {
            $params['logo_url'] = strval($this->logoUrl);
        }

        if (null != $this->companyName) {
            $params['recipient_description'] = strval($this->companyName);
        }

        return SidFactory::createFromSaleResponse(
            $this->request($params, 'https://pay.skrill.com')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function prepareTransfer(TransferRequest $request): Sid
    {
        $params = $request->getPayload();
        $params['action'] = 'prepare';
        $params['email'] = strval($this->merchantEmail);
        $params['password'] = $this->password;

        return SidFactory::createFromXMLResponse(
            $this->request($params, 'https://www.skrill.com/app/pay.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function preparePayout(PayoutRequest $request): Sid
    {
        $params = $request->getPayload();
        $params['action'] = 'prepare';
        $params['email'] = strval($this->merchantEmail);
        $params['password'] = $this->password;

        return SidFactory::createFromXMLResponse(
            $this->request($params, 'https://www.skrill.com/app/pay.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function prepareRefund(RefundRequest $request): Sid
    {
        $params = $request->getPayload();
        $params['action'] = 'prepare';
        $params['email'] = strval($this->merchantEmail);
        $params['password'] = $this->password;

        return SidFactory::createFromXMLResponse(
            $this->request($params, 'https://www.skrill.com/app/refund.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function executeTransfer(Sid $sid): Response
    {
        return ResponseFactory::createFromTransferResponse(
            $this->request(['action' => 'transfer', 'sid' => strval($sid)], 'https://www.skrill.com/app/pay.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function executePayout(Sid $sid): Response
    {
        return ResponseFactory::createFromTransferResponse(
            $this->request(['action' => 'transfer', 'sid' => strval($sid)], 'https://www.skrill.com/app/pay.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function executeRefund(Sid $sid): Response
    {
        return ResponseFactory::createFromRefundResponse(
            $this->request(['action' => 'refund', 'sid' => strval($sid)], 'https://www.skrill.com/app/refund.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function prepareOnDemand(OnDemandRequest $request): Sid
    {
        $params = $request->getPayload();
        $params['action'] = 'prepare';
        $params['email'] = strval($this->merchantEmail);
        $params['password'] = $this->password;

        return SidFactory::createFromXMLResponse(
            $this->request($params, 'https://www.skrill.com/app/ondemand_request.pl')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function executeOnDemand(Sid $sid): Response
    {
        return ResponseFactory::createFromTransferResponse(
            $this->request(
                ['action' => 'request', 'sid' => strval($sid)],
                'https://www.skrill.com/app/ondemand_request.pl'
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException
     */
    public function viewHistory(DateTimeInterface $startDate, DateTimeInterface $endDate = null): ArrayObject
    {
        $params = [
            'email' => strval($this->merchantEmail),
            'password' => $this->password,
            'action' => 'history',
            'start_date' => $startDate->format('d-m-Y'),
        ];

        if (null != $endDate) {
            $params['end_date'] = $endDate->format('d-m-y');
        }

        $tmpFile = new SplFileObject(tempnam(sys_get_temp_dir(), strval(rand())), 'w+');
        $this->client->request(
            'POST',
            'https://www.skrill.com/app/query.pl',
            [
                RequestOptions::FORM_PARAMS => $params,
                RequestOptions::SINK => $tmpFile->getPathname(),
            ]
        );

        if (preg_match('/^[\d]{3}[\t]{2}(.+)$/', $tmpFile->current(), $matches)) {
            throw SkrillResponseException::fromSkillError($matches[1]);
        }

        $result = new ArrayObject();
        $tmpFile->rewind();
        $tmpFile->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);

        foreach (new LimitIterator($tmpFile, 1) as $row) {
            [
                ,
                $time,
                $type,
                $details,
                $lesion,
                $profit,
                $status,
                $balance,
                $reference,
                $amount,
                $currency,
                $info,
                $skrillId,
                $paymentType,
            ] = $row;
            $datetime = DateTimeImmutable::createFromFormat('d M y H:i', $time);

            if (!$datetime instanceof DateTimeImmutable) {
                throw SkrillResponseException::fromSkillError(sprintf('Invalid time "%s".', $time));
            }

            $result->append(
                new HistoryItem(
                    $reference,
                    $skrillId,
                    $datetime,
                    $type,
                    $details,
                    $lesion,
                    $profit,
                    $status,
                    $balance,
                    $amount,
                    $currency,
                    $info,
                    $paymentType
                )
            );
        }

        unlink($tmpFile->getPathname());

        return $result;
    }

    /**
     * @param array  $parameters
     * @param string $url
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    private function request(array $parameters, string $url): ResponseInterface
    {
        return $this->client->request(
            'POST',
            $url,
            [
                RequestOptions::FORM_PARAMS => $parameters,
                RequestOptions::HEADERS => [
                    'Accept' => 'text/xml',
                ],
            ]
        );
    }
}
