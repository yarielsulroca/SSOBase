    public function descargarChecklist(Request $request, int $anexoId)
    {
        $this->dispatch('actualizarJs');
        
        try {
            // Buscar el anexo y su detalle
            $anexo = AnexoMovil::findOrFail($anexoId);
            $detalle = AnexoMovilDetalles::where('anexo_movil_id', $anexoId)->firstOrFail();

            // Generar nombre único para el archivo
            $userId = $request->user()->name;
            $fecha = Carbon::now()->format('Y-m-d-His');
            $this->usuarioPdf = $userId . '-' . $fecha;

            // Generar el PDF
            $pdf = Pdf::loadView('pdf.anexo_movil', [
                'anexo' => $anexo, 
                'detalle' => $detalle,
                'usuario' => $this->usuarioPdf
            ]);

            // Retornar la descarga
            return response()->streamDownload(
                fn() => print($pdf->output()),
                "checklist-anexo-{$anexoId}-{$fecha}.pdf",
                ['Content-Type' => 'application/pdf']
            );

        } catch (ModelNotFoundException $e) {
            Log::warning("Anexo o detalle no encontrado para anexo_id={$anexoId}");
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'No se encontró el detalle del anexo.'
            ]);
        } catch (\Exception $e) {
            Log::error(
                "Error descargando checklist para anexo_id={$anexoId}: "
                . $e->getMessage()
                . "\n"
                . $e->getTraceAsString()
            );

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ocurrió un error al generar el PDF. Por favor, contactá al soporte.'
            ]);
        }
    }
