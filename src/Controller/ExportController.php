<?php
// src/Controller/ExportController.php
namespace App\Controller;

use App\Repository\BeneficiaireRepository;
use App\Repository\ActiviteRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/export')]
class ExportController extends AbstractController
{
    #[Route('/beneficiaires/excel', name: 'app_export_beneficiaires_excel')]
    public function exportBeneficiairesExcel(BeneficiaireRepository $repository): Response
    {
        $beneficiaires = $repository->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prénom');
        $sheet->setCellValue('D1', 'Date Naissance');
        $sheet->setCellValue('E1', 'Genre');
        $sheet->setCellValue('F1', 'Niveau Autisme');
        $sheet->setCellValue('G1', 'Parents');
        $sheet->setCellValue('H1', 'Téléphone');

        // Données
        $row = 2;
        foreach ($beneficiaires as $beneficiaire) {
            $parentsList = [];
            foreach ($beneficiaire->getParents() as $parent) {
                $parentsList[] = $parent->getNom() . ' ' . $parent->getPrenom();
            }

            $sheet->setCellValue('A' . $row, $beneficiaire->getId());
            $sheet->setCellValue('B' . $row, $beneficiaire->getNom());
            $sheet->setCellValue('C' . $row, $beneficiaire->getPrenom());
            $sheet->setCellValue('D' . $row, $beneficiaire->getDateNaissance()->format('d/m/Y'));
            $sheet->setCellValue('E' . $row, $beneficiaire->getGenre());
            $sheet->setCellValue('F' . $row, $beneficiaire->getNiveauAutisme());
            $sheet->setCellValue('G' . $row, implode(', ', $parentsList));
            $sheet->setCellValue('H' . $row, $beneficiaire->getTelephone());
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="beneficiaires.xlsx"');

        return $response;
    }
}