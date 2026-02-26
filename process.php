<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Conversion Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="glass-header py-3 mb-5">
        <div class="container text-center">
            <h2 class="mb-0 gradient-text">CPESFD</h2>
            <span class="text-light opacity-75">Physical Quantities Converter</span>
        </div>
    </header>

    <main>
        <div class="container mt-5">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validation & Setup
            $raw_val = $_POST['num_val'];
            $clean_val = str_replace(',', '', $raw_val);
            
            if (!is_numeric($clean_val)) {
                die("<div class='alert alert-danger'>Error: Invalid numerical input. <a href='index.php'>Go back</a></div>");
            }

            $val = (float)$clean_val;
            $qty = $_POST['quantity'];
            $from_unit = $_POST['from_unit'];
            $mode = $_POST['mode'];
            $dept = ($_POST['dept'] == "Others") ? htmlspecialchars($_POST['other_dept']) : $_POST['dept'];
            $username = htmlspecialchars($_POST['username']);
            
            $base_val = 0;
            $results = [];
            
            // TWO-STEP CONVERSION LOGIC
            if ($qty == "energy") {
                // Step 1: Convert selected 'From Unit' back to standard Joules
                switch($from_unit) {
                    case 'J': $base_val = $val; break;
                    case 'kJ': $base_val = $val * 1000; break;
                    case 'MJ': $base_val = $val * 1000000; break;
                    case 'kWh': $base_val = $val * 3600000; break;
                    case 'cal': $base_val = $val * 4.184; break;
                    case 'kcal': $base_val = $val * 4184; break;
                    case 'BTU': $base_val = $val * 1055.06; break;
                }
                
                // Step 2: Convert standard Joules to all possible outputs
                $results['J'] = ["label" => "Joules", "val" => $base_val];
                $results['kJ'] = ["label" => "Kilojoules", "val" => $base_val / 1000];
                $results['MJ'] = ["label" => "Megajoules", "val" => $base_val / 1000000];
                $results['kWh'] = ["label" => "Kilowatt-Hours", "val" => $base_val / 3600000];
                $results['cal'] = ["label" => "Calories", "val" => $base_val / 4.184];
                $results['kcal'] = ["label" => "Kilocalories", "val" => $base_val / 4184];
                $results['BTU'] = ["label" => "British Thermal Units", "val" => $base_val / 1055.06];
                
            } elseif ($qty == "power") {
                // Step 1: Convert selected 'From Unit' back to standard Watts
                switch($from_unit) {
                    case 'W': $base_val = $val; break;
                    case 'kW': $base_val = $val * 1000; break;
                    case 'MW': $base_val = $val * 1000000; break;
                    case 'cal_s': $base_val = $val * 4.184; break;
                    case 'BTU_h': $base_val = $val / 3.412142; break; 
                    case 'hp_mech': $base_val = $val * 745.699872; break;
                    case 'hp_metric': $base_val = $val * 735.49875; break;
                }
                
                // Step 2: Convert standard Watts to all possible outputs
                $results['W'] = ["label" => "Watts", "val" => $base_val];
                $results['kW'] = ["label" => "Kilowatts", "val" => $base_val / 1000];
                $results['MW'] = ["label" => "Megawatts", "val" => $base_val / 1000000];
                $results['cal_s'] = ["label" => "Calories per sec", "val" => $base_val / 4.184];
                $results['BTU_h'] = ["label" => "BTUs per hour", "val" => $base_val * 3.412142];
                $results['hp_mech'] = ["label" => "Horsepower (mech)", "val" => $base_val / 745.699872];
                $results['hp_metric'] = ["label" => "Horsepower (metric)", "val" => $base_val / 735.49875];
                
            } elseif ($qty == "temp") {
                // Step 1: Convert selected 'From Unit' back to standard Celsius
                switch($from_unit) {
                    case 'C': $base_val = $val; break;
                    case 'F': $base_val = ($val - 32) * 5/9; break;
                    case 'K': $base_val = $val - 273.15; break;
                }
                
                // Step 2: Convert standard Celsius to all possible outputs
                $results['C'] = ["label" => "Celsius", "val" => $base_val];
                $results['F'] = ["label" => "Fahrenheit", "val" => ($base_val * 9/5) + 32];
                $results['K'] = ["label" => "Kelvin", "val" => $base_val + 273.15];
            }

            // Identify the label of the original input for the display text
            $from_label = isset($results[$from_unit]) ? $results[$from_unit]['label'] : "Units";

            echo "<div class='card shadow'><div class='card-header text-center'>";
            echo "<h4>Results for $username ($dept)</h4></div><div class='card-body'>";
            
            // Also adds the unit abbreviation next to the Input display
            echo "<p class='text-center fs-5'><strong>Input:</strong> $val $from_unit</p>";

            // Display Output
            if ($mode == "all") {
                echo "<div class='table-responsive'><table class='table table-striped table-bordered mt-3'>";
                echo "<thead class='table-dark'><tr><th>Target Unit</th><th>Converted Value</th></tr></thead><tbody>";
                
                // Loops through the results to create the rows
                foreach ($results as $key => $data) {
                    // APPENDS THE $key (Unit Abbreviation) HERE
                    echo "<tr><td>{$data['label']}</td><td>" . number_format($data['val'], 6) . " <strong>$key</strong></td></tr>";
                }
                echo "</tbody></table></div>";
            } else {
                if (!empty($_POST['targets'])) {
                    echo "<ul class='list-group mt-3 bg-transparent'>";
                    foreach ($_POST['targets'] as $target) {
                        if (isset($results[$target])) {
                            // APPENDS THE $target (Unit Abbreviation) HERE
                            echo "<li class='list-group-item bg-transparent text-light border-secondary'><strong>" . $results[$target]['label'] . ":</strong> " . number_format($results[$target]['val'], 6) . " <strong>$target</strong></li>";
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<div class='alert alert-warning mt-3'>No specific units selected.</div>";
                }
            }
            echo "<div class='text-center mt-4'><a href='index.php' class='btn btn-secondary px-5'>Convert Another</a></div>";
            echo "</div></div>";
        } else {
            header("Location: index.php");
            exit(); 
        }
        ?>
        </div>
    </main>

    <footer class="glass-footer text-center py-4 mt-5">
        <div class="container">
            <p class="mb-1 text-light fw-bold">
                &copy; 2026 Developed by Sikma Boys | BS CpE 2A
            </p>
            <small class="text-muted">Carlos Hilado Memorial State University</small>
        </div>
    </footer>
</body>
</html>