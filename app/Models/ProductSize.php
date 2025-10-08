<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSize extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'plano_width' => 'decimal:2',
        'plano_height' => 'decimal:2',
        'quantity_per_plano' => 'integer',
        'waste_percentage' => 'decimal:2',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the product that owns the size
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate quantity per plano automatically
     * 
     * @return int|null
     */
    public function calculateQuantityPerPlano(): ?int
    {
        if (!$this->width || !$this->height || !$this->plano_width || !$this->plano_height) {
            return null;
        }

        // Calculate how many pieces fit in one plano
        $piecesWidth = floor($this->plano_width / $this->width);
        $piecesHeight = floor($this->plano_height / $this->height);

        return (int) ($piecesWidth * $piecesHeight);
    }

    /**
     * Calculate cutting efficiency percentage
     * 
     * @return float|null
     */
    public function calculateEfficiency(): ?float
    {
        if (!$this->width || !$this->height || !$this->plano_width || !$this->plano_height) {
            return null;
        }

        $quantityPerPlano = $this->calculateQuantityPerPlano();
        if (!$quantityPerPlano) {
            return null;
        }

        $usedArea = ($this->width * $this->height) * $quantityPerPlano;
        $planoArea = $this->plano_width * $this->plano_height;

        return round(($usedArea / $planoArea) * 100, 2);
    }

    /**
     * Get formatted display string
     * 
     * @return string
     */
    public function getDisplayString(): string
    {
        $display = $this->ukuran_potongan;
        
        if ($this->ukuran_plano) {
            $display .= " (Plano: {$this->ukuran_plano})";
        }

        if ($this->size_name) {
            $display = "{$this->size_name} - {$display}";
        }

        return $display;
    }
}
