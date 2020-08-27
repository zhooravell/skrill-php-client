<?php

declare(strict_types=1);

namespace Skrill;

use ArrayObject;
use LimitIterator;
use Skrill\Factory\VerificationServiceFactory;
use Skrill\Request\CustomerVerificationRequest;
use Skrill\ValueObject\SecretWord;
use SplFileObject;
use DateTimeInterface;
use Skrill\ValueObject\Sid;
use Skrill\ValueObject\Url;
use Skrill\ValueObject\Email;
use Skrill\Response\Response;
use Skrill\Factory\SidFactory;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\ClientInterface;
use Skrill\Request\SaleRequest;
use Skrill\ValueObject\Password;
use Skrill\Request\PayoutRequest;
use Skrill\Request\RefundRequest;
use Skrill\Request\TransferRequest;
use Skrill\Factory\ResponseFactory;
use Skrill\Request\OnDemandRequest;
use Skrill\ValueObject\CompanyName;
use Skrill\Factory\HistoryItemFactory;
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
    SkrillCustomerVerificationClientInterface,
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
     * Skrill Secret Word
     *
     * @var SecretWord|null
     */
    private $secretWord;

    /**
     * @param ClientInterface $client
     * @param Email $merchantEmail
     * @param Password $password
     * @param Url|null $logoUrl
     * @param CompanyName|null $companyName
     * @param SecretWord|null $secretWord
     */
    public function __construct(
        ClientInterface $client,
        Email $merchantEmail,
        Password $password,
        Url $logoUrl = null,
        CompanyName $companyName = null,
        SecretWord $secretWord = null
    ) {
        $this->client = $client;
        $this->companyName = $companyName;
        $this->merchantEmail = $merchantEmail;
        $this->logoUrl = $logoUrl;
        $this->password = md5((string)$password);
        $this->secretWord = $secretWord;
    }


    /**
     * {@inheritdoc}
     * @throws GuzzleException
     */
    public function executeCustomerVerification(CustomerVerificationRequest $request): Response
    {
        $params = $request->getPayload();
        $params['password'] = $this->secretWord;

        return VerificationServiceFactory::createFromCustomerVerificationResponse(
            $this->request(array_filter($params), 'https://api.skrill.com/mqi/customer-verifications', 'json')
        );
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
        $params['pay_to_email'] = (string)$this->merchantEmail;

        if (null != $this->logoUrl) {
            $params['logo_url'] = (string)$this->logoUrl;
        }

        if (null != $this->companyName) {
            $params['recipient_description'] = (string)$this->companyName;
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
        $params['email'] = (string)$this->merchantEmail;
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
        $params['email'] = (string)$this->merchantEmail;
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
        $params['email'] = (string)$this->merchantEmail;
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
            $this->request(['action' => 'transfer', 'sid' => (string)$sid], 'https://www.skrill.com/app/pay.pl')
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
            $this->request(['action' => 'transfer', 'sid' => (string)$sid], 'https://www.skrill.com/app/pay.pl')
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
            $this->request(['action' => 'refund', 'sid' => (string)$sid], 'https://www.skrill.com/app/refund.pl')
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
        $params['email'] = (string)$this->merchantEmail;
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
                ['action' => 'request', 'sid' => (string)$sid],
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
            'email' => (string)$this->merchantEmail,
            'password' => $this->password,
            'action' => 'history',
            'start_date' => $startDate->format('d-m-Y'),
        ];

        if (null != $endDate) {
            $params['end_date'] = $endDate->format('d-m-y');
        }

        $tmpFile = new SplFileObject(tempnam(sys_get_temp_dir(), (string)mt_rand()), 'w+');
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
            $result->append(HistoryItemFactory::createFromRow($row));
        }

        unlink($tmpFile->getPathname());

        return $result;
    }

    /**
     * @param array $parameters
     * @param string $url
     * @param string $type
     * @param string $method
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    private function request(array $parameters, string $url, string $type = 'xml', string $method = 'POST'): ResponseInterface
    {
        return $this->client->request($method, $url, $this->setHeaders($type, $parameters));
    }

    /**
     * @param string $type
     * @param array $parameters
     * @return array
     */
    private function setHeaders(string $type, array $parameters): array
    {
        if (method_exists( $this, "{$type}Headers")) {
            return $this->{"{$type}Headers"}($parameters);
        }
        return [];
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function xmlHeaders(array $parameters): array
    {
        return [
            RequestOptions::FORM_PARAMS => $parameters,
            RequestOptions::HEADERS => ['Accept' => 'text/xml']
        ];
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function jsonHeaders(array $parameters): array
    {
        return [
            RequestOptions::JSON => $parameters,
            RequestOptions::HEADERS => ['Accept' => 'application/json']
        ];
    }
}
