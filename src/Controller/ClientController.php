<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Routing\Annotation\Route;


class ClientController extends AbstractApiController
{

  private $client_manager;

  public function __construct(ClientManagerInterface $client_manager)
  {
    $this->client_manager = $client_manager;
  }

    /**
     * @Route("/createClient", methods={"POST"})
     * @param Request $request
     * @return Response
     */
  public function AuthenticationAction(Request $request): Response
  {
    dump("buradayım");
    $data = json_decode($request->getContent(), true);
    if (empty($data['redirect-uri']) || empty($data['grant-type'])) {
      return $this->respond($data);
    }

    $clientManager = $this->client_manager;
    $client = $clientManager->createClient();
    $client->setRedirectUris([$data['redirect-uri']]);
    $client->setAllowedGrantTypes([$data['grant-type']]);
    $clientManager->updateClient($client);

    $rows = [
      'client_id' => $client->getPublicId(), 'client_secret' => $client->getSecret()
    ];

    return $this->respond($rows);
  }

}
