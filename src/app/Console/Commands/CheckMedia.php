<?php

namespace Ipsum\Media\app\Console\Commands;


use Illuminate\Console\Command;
use Ipsum\Media\app\Models\Media;

class CheckMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:check
                                {--delete-on-error }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie la présence des fichiers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $medias = Media::images()->get();

        $this->info('Vérification des images');

        $this->progressBar = $this->output->createProgressBar($medias->count());
        $this->progressBar->start();

        $errors = collect();
        foreach ($medias as $media) {
            if (!\File::exists(public_path($media->path))) {
                $errors->push($media);
            }
            $this->progressBar->advance();
        }

        $this->progressBar->finish();

        if ($errors->count()) {
            $this->error("\n".'Des fichier sont manquants => ids : '.$errors->pluck('id')->join(','));

            if ($this->option('delete-on-error')) {
                foreach ($errors as $error) {
                    $error->delete();
                }
            }
        }


        return Command::SUCCESS;
    }

}
