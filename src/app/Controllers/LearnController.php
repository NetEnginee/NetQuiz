<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\MaterialRepositoryInterface;

#[Authorize(Role::USER, Role::ADMIN)]
class LearnController extends Controller
{
    public function __construct(
        private MaterialRepositoryInterface $materialRepo,
        private Request $request
    ) {}

    /**
     * Display list of materials grouped by category.
     */
    public function index(): Response
    {
        $materials = $this->materialRepo->getAll();

        $groupedMaterials = [];
        foreach ($materials as $material) {
            $category = $material['category'];
            if (!isset($groupedMaterials[$category])) {
                $groupedMaterials[$category] = [];
            }
            $groupedMaterials[$category][] = $material;
        }

        return $this->view('learn/index', [
            'title' => 'Belajar Materi | NetQuiz',
            'groupedMaterials' => $groupedMaterials
        ]);
    }

    /**
     * View specific material details and related category articles.
     */
    public function viewMaterial(string|int $id): Response
    {
        $id = (int)$id;
        $material = $this->materialRepo->getById($id);

        if (!$material) {
            return $this->view('errors/404', [
                'title' => 'Materi Tidak Ditemukan | NetQuiz'
            ])->setStatusCode(404);
        }

        $otherMaterials = $this->materialRepo->getByCategory($material['category']);
        $otherMaterials = array_values(array_filter($otherMaterials, function ($m) use ($id) {
            return (int)$m['id'] !== $id;
        }));

        // Ensure at least 3 related materials are provided
        if (count($otherMaterials) < 3) {
            $allMaterials = $this->materialRepo->getAll();
            $existingIds = array_map(fn($m) => (int)$m['id'], $otherMaterials);
            $existingIds[] = $id;

            foreach ($allMaterials as $m) {
                if (!in_array((int)$m['id'], $existingIds, true)) {
                    $otherMaterials[] = $m;
                    $existingIds[] = (int)$m['id'];
                    if (count($otherMaterials) >= 3) {
                        break;
                    }
                }
            }
        }

        return $this->view('learn/view', [
            'title' => $material['title'] . ' | NetQuiz',
            'material' => $material,
            'otherMaterials' => $otherMaterials
        ]);
    }
}
