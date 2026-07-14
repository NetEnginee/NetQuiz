<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\Role;
use App\Repositories\MaterialRepository;

#[Authorize(Role::USER, Role::ADMIN)]
class LearnController extends Controller
{
    private MaterialRepository $materialRepo;

    public function __construct()
    {
        $this->materialRepo = new MaterialRepository();
    }

    /**
     * Display list of materials.
     */
    public function index()
    {
        $materials = $this->materialRepo->getAll();

        // Group materials by category for better display
        $groupedMaterials = [];
        foreach ($materials as $material) {
            $category = $material['category'];
            if (!isset($groupedMaterials[$category])) {
                $groupedMaterials[$category] = [];
            }
            $groupedMaterials[$category][] = $material;
        }

        $this->view('learn/index', [
            'title' => 'Belajar Materi | NetQuiz',
            'groupedMaterials' => $groupedMaterials
        ]);
    }

    /**
     * View specific material details.
     */
    public function viewMaterial($id)
    {
        $id = (int)$id;
        $material = $this->materialRepo->getById($id);

        if (!$material) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Materi Tidak Ditemukan | NetQuiz'
            ]);
            return;
        }

        // Get related materials in the same category
        $otherMaterials = $this->materialRepo->getByCategory($material['category']);
        // Exclude the current material
        $otherMaterials = array_filter($otherMaterials, function($m) use ($id) {
            return (int)$m['id'] !== $id;
        });

        $this->view('learn/view', [
            'title' => $material['title'] . ' | NetQuiz',
            'material' => $material,
            'otherMaterials' => $otherMaterials
        ]);
    }
}
