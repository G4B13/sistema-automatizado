<?php

declare(strict_types=1);

/**
 * Ícones dos lembretes.
 *
 * São desenhos simples e reconhecíveis: no totem, a pessoa identifica a
 * rotina pelo ícone antes de ler o título.
 */
const ICONES = [
    'relogio'  => ['rotulo' => 'Horário',   'svg' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
    'remedio'  => ['rotulo' => 'Remédio',   'svg' => '<rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/>'],
    'refeicao' => ['rotulo' => 'Refeição',  'svg' => '<path d="M5 3v8a2 2 0 0 0 4 0V3M7 11v10"/><path d="M17 3c-1.5 2-2 4-2 6h4c0-2-.5-4-2-6zM17 9v12"/>'],
    'agua'     => ['rotulo' => 'Água',      'svg' => '<path d="M12 3s6 6.5 6 10.5A6 6 0 0 1 6 13.5C6 9.5 12 3 12 3z"/>'],
    'higiene'  => ['rotulo' => 'Higiene',   'svg' => '<path d="M9 3h6v5H9zM8 8h8l-1 13H9z"/>'],
    'rotina'   => ['rotulo' => 'Rotina',    'svg' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/>'],
    'escola'   => ['rotulo' => 'Escola',    'svg' => '<path d="M3 9l9-5 9 5-9 5z"/><path d="M7 12v5c0 1.5 2.5 3 5 3s5-1.5 5-3v-5"/>'],
    'exercicio'=> ['rotulo' => 'Exercício', 'svg' => '<path d="M4 9v6M20 9v6M7 6v12M17 6v12M7 12h10"/>'],
];

function icone_svg(string $chave, int $tamanho = 26): string
{
    $desenho = ICONES[$chave]['svg'] ?? ICONES['relogio']['svg'];

    return sprintf(
        '<svg viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor"'
        . ' stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
        $tamanho,
        $tamanho,
        $desenho
    );
}
