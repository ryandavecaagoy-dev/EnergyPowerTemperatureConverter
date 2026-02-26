<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Physical Quantities Converter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
        // Data from RealCalc screenshots
        const units = {
            energy: [
                {val: 'J', label: 'Joules'}, {val: 'kJ', label: 'Kilojoules'}, 
                {val: 'MJ', label: 'Megajoules'}, {val: 'kWh', label: 'Kilowatt-Hours'}, 
                {val: 'cal', label: 'Calories'}, {val: 'kcal', label: 'Kilocalories'}, 
                {val: 'BTU', label: 'British Thermal Units'}
            ],
            power: [
                {val: 'W', label: 'Watts'}, {val: 'kW', label: 'Kilowatts'}, 
                {val: 'MW', label: 'Megawatts'}, {val: 'cal_s', label: 'Calories per sec'}, 
                {val: 'BTU_h', label: 'BTUs per hour'}, {val: 'hp_mech', label: 'Horsepower (mech)'}, 
                {val: 'hp_metric', label: 'Horsepower (metric)'}
            ],
            temp: [
                {val: 'C', label: 'Celsius'}, {val: 'F', label: 'Fahrenheit'}, 
                {val: 'K', label: 'Kelvin'}
            ]
        };

        function toggleOther(val) {
            document.getElementById('otherDept').style.display = (val === 'Others') ? 'block' : 'none';
        }

        function updateSelectors() {
            const qty = document.getElementById('quantitySelect').value;

            // 1. Update "From Unit" Dropdown
            const fromUnit = document.getElementById('fromUnitSelect');
            fromUnit.innerHTML = '';
            units[qty].forEach(unit => {
                fromUnit.add(new Option(unit.label, unit.val));
            });

            // 2. Update "To" Targets Checkboxes
            const container = document.getElementById('checkboxContainer');
            container.innerHTML = ''; // Clear current checkboxes
            units[qty].forEach(unit => {
                const div = document.createElement('div');
                div.className = 'form-check form-check-inline';
                div.innerHTML = `
                    <input class="form-check-input specific-checkbox" type="checkbox" name="targets[]" value="${unit.val}">
                    <label class="form-check-label text-light">${unit.label}</label>
                `;
                container.appendChild(div);
            });
        }

       function toggleSpecificMode() {
            const isSpecific = document.getElementById('modeSpecific').checked;
            document.getElementById('checkboxContainerWrapper').style.display = isSpecific ? 'flex' : 'none';
        }
        
        // Initialize on load
        window.onload = function() {
            updateSelectors();
            toggleSpecificMode();
        };
    </script>
</head>
<body>
    <header class="glass-header py-3 mb-5">
        <div class="container text-center">
            <h2 class="mb-0 gradient-text">CPESFD Lab 2</h2>
            <span class="text-light opacity-75">Physical Quantities Converter</span>
        </div>
    </header>

    <main>
        <div class="container mt-4">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h3>Energy, Power, and Temperature Converter</h3>
                </div>
                <div class="card-body">
                    <form action="process.php" method="POST">
                        <div class="row striped-row align-items-center">
                            <div class="col-sm-4 text-sm-end">
                                <label class="form-label">Full Name:</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" name="username" class="form-control" placeholder="Enter your name" required>
                            </div>
                        </div>

                        <div class="row striped-row align-items-center">
                            <div class="col-sm-4 text-sm-end">
                                <label class="form-label">Department:</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="dept" class="form-select" onchange="toggleOther(this.value)">
                                    <option value="Engineering">Engineering</option>
                                    <option value="Computer Studies">Computer Studies</option>
                                    <option value="Industrial Technology">Industrial Technology</option>
                                    <option value="Education">Education</option>
                                    <option value="Others">Others</option>
                                </select>
                                <input type="text" name="other_dept" id="otherDept" class="form-control mt-2" style="display:none;" placeholder="Please specify department">
                            </div>
                        </div>

                        <div class="row striped-row align-items-center">
                            <div class="col-sm-4 text-sm-end">
                                <label class="form-label">Physical Quantity:</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="quantity" id="quantitySelect" class="form-select" onchange="updateSelectors()">
                                    <option value="energy">Energy</option>
                                    <option value="power">Power</option>
                                    <option value="temp">Temperature</option>
                                </select>
                            </div>
                        </div>

                        <div class="row striped-row align-items-center">
                            <div class="col-sm-4 text-sm-end">
                                <label class="form-label">Value & Unit:</label>
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" name="num_val" class="form-control" placeholder="E.g., 100.5" pattern="[0-9.,-]+" title="Valid numerical symbols only" required>
                                    <select name="from_unit" id="fromUnitSelect" class="form-select" style="max-width: 250px;">
                                        </select>
                                </div>
                                <small class="text-muted d-block mt-1">Accepts numbers, commas, periods, and negatives.</small>
                            </div>
                        </div>

                        <div class="row striped-row align-items-center">
                            <div class="col-sm-4 text-sm-end">
                                <label class="form-label">Conversion Mode:</label>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mode" id="modeAll" value="all" onchange="toggleSpecificMode()" checked>
                                    <label class="form-check-label text-light">All Available</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mode" id="modeSpecific" value="specific" onchange="toggleSpecificMode()">
                                    <label class="form-check-label text-light">Specific Target</label>
                                </div>
                            </div>
                        </div>

                        <div class="row striped-row align-items-center" id="checkboxContainerWrapper" style="display:none;">
                            <div class="col-sm-4 text-sm-end">
                                <label class="form-label">Select Targets:</label>
                            </div>
                            <div class="col-sm-8">
                                <div id="checkboxContainer" class="p-2 border rounded" style="background: rgba(0,0,0,0.2);">
                                    </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary w-50 py-2 fs-5">Calculate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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