<?php

// * Dossier contenant les logs
$logDir = ROOT_PATH . "/logs";

// * Récupère les fichiers log disponibles
$logFiles = glob($logDir . "/main-*.log");

// * Trie les fichiers par date décroissante
usort($logFiles, function ($a, $b) {
    return strcmp($b, $a);
});

// * Extrait les dates des fichiers
$logDates = array_map(function ($file) {
    return str_replace(["main-", ".log"], "", basename($file));
}, $logFiles);

// * Détermine la date sélectionnée (par défaut, la plus récente)
$selectedDate = $_GET["date"] ?? ($logDates[0] ?? null);

// * Charge le contenu du fichier sélectionné
$logContent = "";
if ($selectedDate && in_array($selectedDate, $logDates)) {
    $filePath = "$logDir/main-$selectedDate.log";
    if (file_exists($filePath)) {
        $logContent = file_get_contents($filePath);
    }
}

function parseLogs($content)
{
    $lines = explode("\n", $content);
    $output = "";
    foreach ($lines as $line) {
        if (empty(trim($line))) continue;
        preg_match("/^\[(.*?)\] (\w+)\.(\w+): (.*?)(?: \{(.*)\})? \[\]$/", $line, $matches);
        if ($matches) {
            [$_, $timestamp, $channel, $level, $message, $json] = $matches;
            $levelClass = strtoupper($level);
            $formattedJson = "";
            if (!empty($json)) {
                $jsonStr = "{" . $json . "}";
                $decoded = json_decode($jsonStr, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $formattedJson = "<pre>" . htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . "</pre>";
                }
            }
            $output .= <<<HTML
                <div class="log-entry $levelClass">
                    <div class="timestamp">[$timestamp] <strong>[$level]</strong></div>
                    <div class="message">$message</div>
                    $formattedJson
                </div>
            HTML;
        }
    }
    return $output ?: "<p>Aucune entrée trouvée.</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Logs Visualizer</title>
    <link rel="stylesheet" href="/public/logs/index.css">
</head>

<body>
    <h1>Visualiseur de Logs</h1>
    <form method="GET">
        <label for="date">Choisir une date :</label>
        <select name="date" id="date" onchange="this.form.submit()">
            <?php foreach ($logDates as $date): ?>
                <option value="<?= htmlspecialchars($date) ?>" <?= $date === $selectedDate ? 'selected' : '' ?>>
                    <?= htmlspecialchars($date) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit">Charger</button></noscript>
    </form>

    <hr>

    <h2>Logs du <?= htmlspecialchars($selectedDate) ?></h2>
    <div id="logs">
        <?= parseLogs($logContent) ?>
    </div>

</body>

</html>