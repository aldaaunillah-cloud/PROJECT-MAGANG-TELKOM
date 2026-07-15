@extends('layouts.app')

@section('page-title', 'REKAP AGENCY')
@section('page-subtitle', 'Penyelesaian Billing 1 dan 2 - Agensi Witel Priangan Timur')

@section('content')

<div class="filter-box">
    <div class="row align-items-end">
        <div class="col-md-3">
            <label>AGENCY</label>
            <select class="form-select">
                <option>Semua Agency</option>
            </select>
        </div>
        <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
            <button class="btn btn-filter px-4">
                <i class="bi bi-funnel-fill me-2"></i>Terapkan Filter
            </button>
            <button class="btn btn-reset px-4">
                <i class="bi bi-arrow-repeat me-2"></i>Reset
            </button>
        </div>
    </div>
</div>

<div class="table-responsive mt-4">
    <h6 style="font-weight:700; font-size:14px; text-transform:uppercase; margin-bottom: 15px;">REKAP BILLING 1 HINGGA 6</h6>
    <table class="table table-bordered align-middle text-center table-hover" style="font-size: 12px; white-space: nowrap; border-color: #cbd5e1;">
        <thead style="border-bottom: 2px solid #94a3b8;">
            <tr>
                <th rowspan="2" class="align-middle" style="background-color: #e2e8f0; min-width: 200px;">DATEL</th>
                <th colspan="3" style="background-color: #bae6fd;">BILLING KE 1</th>
                <th colspan="3" style="background-color: #fed7aa;">BILLING KE 2</th>
                <th colspan="3" style="background-color: #99f6e4;">BILLING KE 3</th>
                <th colspan="3" style="background-color: #fef08a;">BILLING KE 4</th>
                <th colspan="3" style="background-color: #e9d5ff;">BILLING KE 5</th>
                <th colspan="3" style="background-color: #fbcfe8;">BILLING KE 6</th>
                <th colspan="2" style="background-color: #bae6fd;">TOTAL ALL BILLING</th>
                <th rowspan="2" class="align-middle" style="background-color: #e2e8f0;">REWARD</th>
            </tr>
            <tr>
                <th style="background-color: #bae6fd;">SSL</th>
                <th style="background-color: #bae6fd;">RUPIAH</th>
                <th style="background-color: #bae6fd;">%</th>
                <th style="background-color: #fed7aa;">SSL</th>
                <th style="background-color: #fed7aa;">RUPIAH</th>
                <th style="background-color: #fed7aa;">%</th>
                <th style="background-color: #99f6e4;">SSL</th>
                <th style="background-color: #99f6e4;">RUPIAH</th>
                <th style="background-color: #99f6e4;">%</th>
                <th style="background-color: #fef08a;">SSL</th>
                <th style="background-color: #fef08a;">RUPIAH</th>
                <th style="background-color: #fef08a;">%</th>
                <th style="background-color: #e9d5ff;">SSL</th>
                <th style="background-color: #e9d5ff;">RUPIAH</th>
                <th style="background-color: #e9d5ff;">%</th>
                <th style="background-color: #fbcfe8;">SSL</th>
                <th style="background-color: #fbcfe8;">RUPIAH</th>
                <th style="background-color: #fbcfe8;">%</th>
                <th style="background-color: #bae6fd;">SSL</th>
                <th style="background-color: #bae6fd;">RUPIAH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-start fw-bold">91405 - Kuningan</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td>6</td><td>5.155.950</td><td>87,31%</td>
                <td>11</td><td>6.006.676</td><td>83,68%</td>
                <td>6</td><td>4.844.040</td><td>92,18%</td>
                <td>5</td><td>4.644.240</td><td>92,76%</td>
                <td>460</td><td>296.977.601</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91407 - Inner - Priangan Timur</td>
                <td>2</td><td>384420</td><td>98,67%</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td>3</td><td>1.309.800</td><td>96,49%</td>
                <td>5</td><td>2.342.100</td><td>92,55%</td>
                <td>7</td><td>3.674.082</td><td>92,49%</td>
                <td>1</td><td>782.550</td><td>97,40%</td>
                <td>402</td><td>210.886.903</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91403 - Garut</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td>10</td><td>4.236.233</td><td>95,48%</td>
                <td>4</td><td>2.296.035</td><td>95,13%</td>
                <td>9</td><td>6.081.135</td><td>90,10%</td>
                <td>16</td><td>7.203.012</td><td>88,89%</td>
                <td>10</td><td>3.891.882</td><td>94,64%</td>
                <td>718</td><td>381.130.170</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91406 - Majalengka</td>
                <td>2</td><td>449875</td><td>96,44%</td>
                <td>1</td><td>593.850</td><td>98,35%</td>
                <td>3</td><td>1.136.640</td><td>95,63%</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td>1</td><td>394.050</td><td>98,49%</td>
                <td>7</td><td>1.620.045</td><td>94,93%</td>
                <td>375</td><td>158.460.532</td>
                <td class="fw-bold">150.000</td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91404 - Indramayu</td>
                <td>5</td><td>726891</td><td>97,67%</td>
                <td>1</td><td>394.050</td><td>99,30%</td>
                <td>10</td><td>2.978.241</td><td>89,60%</td>
                <td>15</td><td>7.728.375</td><td>86,06%</td>
                <td>7</td><td>2.374.901</td><td>94,32%</td>
                <td>5</td><td>1.933.176</td><td>95,92%</td>
                <td>536</td><td>261.024.383</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91408 - Singaparna</td>
                <td>2</td><td>502133</td><td>97,66%</td>
                <td>3</td><td>706.515</td><td>98,19%</td>
                <td>14</td><td>10.106.677</td><td>70,80%</td>
                <td>4</td><td>2.974.800</td><td>90,48%</td>
                <td>6</td><td>3.174.600</td><td>92,76%</td>
                <td>7</td><td>3.392.826</td><td>92,84%</td>
                <td>438</td><td>217.739.801</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91409 - Tasikmalaya</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td>2</td><td>962.038</td><td>97,11%</td>
                <td>2</td><td>2.220</td><td>99,99%</td>
                <td>2</td><td>2.220</td><td>99,99%</td>
                <td>5</td><td>124.320</td><td>99,61%</td>
                <td>5</td><td>2.497.500</td><td>93,73%</td>
                <td>324</td><td>175.092.620</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-start fw-bold">91401 - Banjar</td>
                <td></td><td></td><td style="color: #22c55e;">100,00%</td>
                <td>1</td><td>394.050</td><td>98,60%</td>
                <td>7</td><td>4.534.572</td><td>84,73%</td>
                <td>4</td><td>1.343.544</td><td>94,37%</td>
                <td>6</td><td>1.653.900</td><td>92,91%</td>
                <td>5</td><td>2.219.334</td><td>94,26%</td>
                <td>308</td><td>158.113.726</td>
                <td></td>
            </tr>
        </tbody>
        <tfoot style="background-color: #e2e8f0; font-weight: bold;">
            <tr>
                <td class="text-start">Grand Total</td>
                <td>11</td><td>2063319</td><td>98,93%</td>
                <td>18</td><td>7.286.736</td><td>98,12%</td>
                <td>49</td><td>27.520.135</td><td>89,81%</td>
                <td>50</td><td>26.478.850</td><td>91,02%</td>
                <td>54</td><td>23.442.905</td><td>93,16%</td>
                <td>45</td><td>20.981.553</td><td>94,36%</td>
                <td>3561</td><td>1.859.425.736</td>
                <td>150.000</td>
            </tr>
        </tfoot>
    </table>
</div>

@endsection
