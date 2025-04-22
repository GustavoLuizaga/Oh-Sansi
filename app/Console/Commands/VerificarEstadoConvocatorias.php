<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VerificarEstadoConvocatorias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convocatorias:verificar-estado';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y actualiza el estado de las convocatorias según sus fechas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Verificando estado de convocatorias...');
        
        try {
            $hoy = Carbon::now();
            
            // Buscar convocatorias publicadas con fecha fin pasada
            $convocatoriasVencidas = DB::table('convocatoria')
                ->where('estado', 'Publicada')
                ->where('fechaFin', '<', $hoy->format('Y-m-d'))
                ->get();
            
            $count = 0;
            
            // Actualizar el estado de las convocatorias vencidas a 'Borrador'
            foreach ($convocatoriasVencidas as $convocatoria) {
                DB::table('convocatoria')
                    ->where('idConvocatoria', $convocatoria->idConvocatoria)
                    ->update([
                        'estado' => 'Borrador',
                        'updated_at' => now()
                    ]);
                
                $this->info("Convocatoria {$convocatoria->idConvocatoria} cambió a estado Borrador por fecha vencida");
                Log::info("Convocatoria {$convocatoria->idConvocatoria} cambió automáticamente a estado Borrador por fecha vencida");
                $count++;
            }
            
            $this->info("Proceso completado. {$count} convocatorias actualizadas.");
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al verificar estado de convocatorias: ' . $e->getMessage());
            Log::error('Error al verificar estado de convocatorias: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}