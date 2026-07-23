<?php

namespace App\Support;

use App\Models\Proposal;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class ProposalPdfRenderer
{
    public function render(Proposal $proposal): string
    {
        $workDirectory = storage_path('app/private/proposal-pdf/'.(string) Str::uuid());
        $htmlPath = $workDirectory.DIRECTORY_SEPARATOR.'proposal.html';
        $pdfPath = $workDirectory.DIRECTORY_SEPARATOR.'proposal.pdf';
        $profilePath = $workDirectory.DIRECTORY_SEPARATOR.'chrome-profile';

        File::ensureDirectoryExists($workDirectory);

        try {
            File::put($htmlPath, view('admin.proposals.export', [
                'proposal' => $proposal,
            ])->render());

            $process = new Process(
                $this->browserCommand($htmlPath, $pdfPath, $profilePath),
                base_path(),
                null,
                null,
                (float) config('proposals.pdf.timeout', 90)
            );
            $process->run();

            if (! $process->isSuccessful()) {
                throw new RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput()));
            }

            if (! File::exists($pdfPath) || File::size($pdfPath) === 0) {
                throw new RuntimeException('The browser PDF export finished without creating a PDF file.');
            }

            return File::get($pdfPath);
        } finally {
            File::deleteDirectory($workDirectory);
        }
    }

    private function browserCommand(string $htmlPath, string $pdfPath, string $profilePath): array
    {
        return [
            $this->browserExecutable(),
            '--headless=new',
            '--disable-gpu',
            '--disable-dev-shm-usage',
            '--no-sandbox',
            '--allow-file-access-from-files',
            '--run-all-compositor-stages-before-draw',
            '--virtual-time-budget=2500',
            '--no-pdf-header-footer',
            '--print-to-pdf-no-header',
            '--print-to-pdf='.$pdfPath,
            '--user-data-dir='.$profilePath,
            $this->fileUri($htmlPath),
        ];
    }

    private function browserExecutable(): string
    {
        $configuredPath = config('proposals.pdf.browser_path');

        if (is_string($configuredPath) && $configuredPath !== '' && File::exists($configuredPath)) {
            return $configuredPath;
        }

        $candidates = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return PHP_OS_FAMILY === 'Windows' ? 'chrome.exe' : 'google-chrome';
    }

    private function fileUri(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $segments = array_map('rawurlencode', explode('/', $path));

        if (isset($segments[0]) && preg_match('/^[A-Za-z]%3A$/', $segments[0]) === 1) {
            $segments[0] = str_replace('%3A', ':', $segments[0]);
        }

        return 'file:///'.implode('/', $segments);
    }
}
