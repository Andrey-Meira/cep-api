<?php

namespace App\Controller;

use App\Service\CepService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CepController extends AbstractController
{
    private CepService $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    public function buscarCep(string $cep): JsonResponse
    {
        try {
            return $this->json($this->cepService->buscarCep($cep));
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erro interno no servidor'], 500);
        }
    }
}
