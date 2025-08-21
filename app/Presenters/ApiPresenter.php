<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ProductRepository;
use Nette\Application\UI\Presenter;
use Nette\Application\Responses\JsonResponse;
use Nette\Http\IResponse;

/**
 * Minimal REST-ish API presenter
 */
final class ApiPresenter extends Presenter
{
    public function __construct(private ProductRepository $products)
    {
        parent::__construct();
    }

    public function actionProducts(?int $id = null): void
    {
        $httpResp = $this->getHttpResponse();
        $httpResp->setHeader('Content-Type', 'application/json');

        if ($this->getHttpRequest()->getMethod() === 'POST') {
            $raw = (string) $this->getHttpRequest()->getRawBody();
            $payload = json_decode($raw, true);

            if (!is_array($payload) || !isset($payload['name'], $payload['price'])) {
                $httpResp->setCode(IResponse::S400_BAD_REQUEST);
                $this->sendResponse(new JsonResponse(['error' => 'Invalid payload']));
            }

            $newId = $this->products->create([
                'name'  => (string) $payload['name'],
                'price' => (float) $payload['price'],
                'sku'   => $payload['sku'] ?? null,
            ]);

            $httpResp->setCode(IResponse::S201_CREATED);
            $this->sendResponse(new JsonResponse(['id' => $newId]));
        }

        if ($id) {
            $item = $this->products->find($id);
            if (!$item) {
                $httpResp->setCode(IResponse::S404_NOT_FOUND);
                $this->sendResponse(new JsonResponse(['error' => 'Not found']));
            }

            $this->sendResponse(new JsonResponse($item));
        }

        $this->sendResponse(new JsonResponse($this->products->all()));
    }
}
