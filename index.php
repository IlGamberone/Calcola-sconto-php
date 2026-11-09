<?php
$mostra_risultato = false;

$errore_prodotto = "";
$errore_prezzo = "";
$errore_sconto = "";

$valore_prodotto = "";
$valore_prezzo = "";
$valore_sconto = "";

$nomeFile = "log.csv";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $valore_prodotto = isset($_POST['prodotto']) ? trim(htmlspecialchars($_POST['prodotto'])) : '';
    $valore_prezzo = isset($_POST['prezzo']) ? htmlspecialchars($_POST['prezzo']) : '';
    $valore_sconto = isset($_POST['sconto']) ? htmlspecialchars($_POST['sconto']) : '';

    if ($valore_prodotto === '') {
        $errore_prodotto = "Campo obbligatorio";
    } elseif (strlen($valore_prodotto) > 30) {
        $errore_prodotto = "Massimo 30 caratteri";
    }

    $prezzo = isset($_POST['prezzo']) ? floatval(str_replace(',', '.', $_POST['prezzo'])) : 0;
    if ($prezzo <= 0) {
        $errore_prezzo = "Valore non valido";
    }

    $sconto = isset($_POST['sconto']) ? intval($_POST['sconto']) : 0;
    if ($sconto < 1 || $sconto > 90) {
        $errore_sconto = "Valore da 1 a 90";
    }

    if ($errore_prodotto === '' && $errore_prezzo === '' && $errore_sconto === '') {
        $risparmio = round($prezzo * $sconto / 100, 2);
        $finale = round($prezzo - $risparmio, 2);

        $prezzo_formatted = number_format($prezzo, 2, '.', '');
        $finale_formatted = number_format($finale, 2, '.', '');
        $risparmio_formatted = number_format($risparmio, 2, '.', '');

        if ($sconto < 20) {
            $categoria = "PICCOLO";
            $class_card = "piccolo";
        } elseif ($sconto < 50) {
            $categoria = "BUONO";
            $class_card = "buono";
        } else {
            $categoria = "SUPER";
            $class_card = "super";
        }

        $data = date("d/m/Y H:i:s");
        $nome_csv = str_replace(",", " ", $valore_prodotto);
        $record = implode("," , [$data , $nome_csv , $prezzo_formatted , $sconto , $finale_formatted , $risparmio_formatted , $categoria , ]). PHP_EOL;

        $file = fopen($nomeFile, "a");
        fwrite($file, $record);
        fclose($file);
        
        $mostra_risultato = true;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mostra_risultato ? 'Risultato sconto' : 'Calcolatore di sconto' ?></title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>

    <div class="bg-container">
        <?php if ($mostra_risultato): ?>
            <h1>Il tuo sconto</h1>
            
            <div class="card-result card <?= $class_card ?>">
                <div class="nome"><?= $valore_prodotto ?></div>
                <div class="dettagli">Sconto del <?= $sconto ?>% · Risparmio € <?= number_format($risparmio, 2, ',', '.') ?></div>
                <div class="valore">€ <?= number_format($finale, 2, ',', '.') ?></div>
                <div class="fascia"><strong><?= $categoria ?></strong></div>
            </div>

            <a href="index.php" class="link-back">Calcola di nuovo</a>
        <?php else: ?>
            <h1>Calcolatore di sconto</h1>
            <div class="card-form">
                <form action="index.php" method="POST" novalidate>
                    <div class="form-group">
                        <label for="prodotto">Prodotto</label>
                        <input type="text" id="prodotto" name="prodotto" value="<?= $valore_prodotto ?>" placeholder="Es. Scarpe">
                        <?php if ($errore_prodotto !== ''): ?>
                            <div class="msg-errore"><?= $errore_prodotto ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="prezzo">Prezzo (€)</label>
                        <input type="text" id="prezzo" name="prezzo" value="<?= $valore_prezzo ?>" placeholder="Es. 50.00">
                        <?php if ($errore_prezzo !== ''): ?>
                            <div class="msg-errore"><?= $errore_prezzo ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="sconto">Sconto (%)</label>
                        <input type="text" id="sconto" name="sconto" value="<?= $valore_sconto ?>" placeholder="Es. 20">
                        <?php if ($errore_sconto !== ''): ?>
                            <div class="msg-errore"><?= $errore_sconto ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit">Calcola</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>