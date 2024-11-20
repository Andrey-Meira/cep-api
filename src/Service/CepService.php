<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CepService
{
    private HttpClientInterface $httpClient;

    private const TEMPO_EXPIRACAO = 86400; // 24 horas

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function buscarCep(string $cep): array
    {
        // Válida se o cep é válido antes de fazer a busca na API externa
        if (!$this->isCepValido($cep)) {
            throw new \InvalidArgumentException('CEP inválido');
        }

        $cache = new RedisAdapter(RedisAdapter::createConnection($_ENV['REDIS_URL']));

        $respCache = $this->getCachedData($cep, $cache);

        if ($respCache['hasCache']) {
            return $respCache['cache'];
        }

        $cachedData = $respCache['cache'];

        $responseEndereco = $this->buscarEnderecoViaCep($cep);

        // Montando ibjeto de endereço
        $endereco = $this->formataDadosEndereco($responseEndereco);

        // Salva no cache por 24 horas
        $cachedData->set($endereco);
        $cachedData->expiresAfter(self::TEMPO_EXPIRACAO);
        $cache->save($cachedData);

        return $endereco;
    }

    private function isCepValido(string $cep): bool
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $cep);

        // Verifica se o CEP possui exatamente 8 dígitos
        return preg_match('/^[0-9]{8}$/', $cep) === 1;
    }

    private function getCachedData($cep, $cache)
    {
        $cacheKey = "cep_$cep";
        // Verifica se o CEP já está em cache
        $cachedData = $cache->getItem($cacheKey);
        if ($cachedData->isHit()) {
            return [
                'hasCache' => true,
                'cache' => $cachedData->get()
            ];
        }

        return [
            'hasCache' => false,
            'cache' => $cachedData
        ];
    }

    private function buscarEnderecoViaCep($cep)
    {
        // Busca o endereço pelo cep na API viacep
        $response = $this->httpClient->request('GET', "https://viacep.com.br/ws/$cep/json/");
        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new \RuntimeException('CEP não encontrado', $statusCode);
        }

        return $response->toArray();
    }

    private function formataDadosEndereco($endereco)
    {
        return [
            'cep' => $endereco['cep'] ?? null,
            'logradouro' => $endereco['logradouro'] ?? null,
            'bairro' => $endereco['bairro'] ?? null,
            'cidade' => $endereco['localidade'] ?? null,
            'estado' => $endereco['uf'] ?? null,
        ];
    }
}
