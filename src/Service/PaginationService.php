<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

final class PaginationService
{
    public function paginate(Request $request, int $defaultPage = 1, int $defaultLimit = 20): array
    {
        $page = max($defaultPage, (int) $request->query->get('page', $defaultPage));
        $limit = max(1, (int) $request->query->get('limit', $defaultLimit));

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => ($page - 1) * $limit,
        ];
    }
}
