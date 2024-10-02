<?php

namespace App\Http\Controllers;

use Gemini;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $yourApiKey = getenv('GEMINI_KEY');
            $client = Gemini::client($yourApiKey);

            // Monta o prompt baseado no conteúdo enviado do front
            $prompt = $request->post('content');

            // Envia o prompt para o Gemini e recebe a resposta
            $result = $client->geminiPro()->generateContent($prompt);

            // Cria um novo documento Word
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Verifica se uma imagem foi enviada e adiciona no topo do documento
            if ($request->hasFile('headerImage')) {
                $image = $request->file('headerImage');

                // Verifica se o arquivo foi carregado corretamente
                if ($image->isValid()) {
                    // Define o caminho temporário da imagem
                    $imagePath = $image->getPathname();

                    // Adiciona a imagem ao documento antes das questões
                    $section->addImage($imagePath, [
                        'width' => 600,
                        'height' => 100,
                        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                    ]);
                } else {
                    return response()->json(['error' => 'Erro ao carregar a imagem.'], Response::HTTP_BAD_REQUEST);
                }
            }

            // Define os estilos de fonte e parágrafos
            $phpWord->addFontStyle('TitleStyle', [
                'name' => 'Arial',
                'size' => 16,
                'bold' => true,
                'color' => '000000'
            ]);

            $phpWord->addFontStyle('QuestionStyle', [
                'name' => 'Arial',
                'size' => 12,
                'bold' => false,
                'color' => '000000'
            ]);

            $phpWord->addParagraphStyle('Centered', [
                'alignment' => 'center',
                'spaceAfter' => 100
            ]);

            $phpWord->addParagraphStyle('Justify', [
                'alignment' => 'both',
                'spaceAfter' => 100
            ]);

            // Adiciona o título ao documento
            $section->addText('Questões Geradas', 'TitleStyle', 'Centered');

            // Adiciona as questões geradas ao documento com formatação
            $questions = explode("\n", $result->text());
            foreach ($questions as $question) {
                $section->addText($question, 'QuestionStyle', 'Justify');
            }

            // Salvar o documento completo
            $fileName = 'questoes-geradas.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'docx');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);

            // Define os cabeçalhos para forçar o download
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Erro ao gerar as questões.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
