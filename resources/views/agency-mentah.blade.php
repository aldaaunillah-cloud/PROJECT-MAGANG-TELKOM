@extends('layouts.app')

@section('page-title', 'AGENCY MENTAH')
@section('page-subtitle', 'Daftar seluruh data agency Telkom beserta dealer dan PIC Sales')

@section('content')

<div class="filter-box">
    <div class="row mb-3">
        <div class="col-md-2">
            <label>Pilih Dealer</label>
            <select class="form-select">
                <option>Semua Dealer</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Pilih STO</label>
            <select class="form-select">
                <option>Semua STO</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Pilih Sales</label>
            <select class="form-select">
                <option>Semua Sales</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Pilih Package</label>
            <select class="form-select">
                <option>Semua Package</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end justify-content-end">
            <div class="input-group w-100">
                <input type="text" class="form-control" placeholder="Cari Dealer / Agency..." style="border-radius: 20px 0 0 20px;">
                <button class="btn btn-outline-secondary bg-white" style="border-radius: 0 20px 20px 0; border-color: #dee2e6;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Tabel 1: PENYELESAIAN BILLING 1 DAN 2 -->
    <div class="col-12 mb-5">
        <div class="table-responsive">
            <h6 class="text-center fw-bold mb-3" style="font-size: 13px;">PENYELESAIAN BILLING 1 DAN 2<br>AGENCY WITEL PRIANGAN TIMUR</h6>
            <table class="table table-bordered align-middle text-center" style="font-size: 12px; white-space: nowrap; border-color: #000;">
                <thead style="background-color: #176B87; color: white; border-color: #000;">
                    <tr>
                        <th class="py-2">AGENCY</th>
                        <th colspan="2" class="py-2">Billing ke 1</th>
                        <th colspan="2" class="py-2">Billing ke 2</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Rupiah</th>
                    </tr>
                </thead>
                <tbody style="border-color: #000;">
                    <tr style="background-color: #D4E6F1; font-weight: bold;">
                        <td class="text-start">- PT. Panthera Mega Cipta</td>
                        <td>1</td><td style="color: red;">29.487</td>
                        <td>12</td><td style="color: red;">4.041.038</td>
                        <td>13</td><td style="color: red;">4.070.525</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Alifa Mayori</td>
                        <td></td><td></td>
                        <td>6</td><td>1.646.492</td>
                        <td>6</td><td>1.646.492</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Fhaily</td>
                        <td></td><td></td>
                        <td>4</td><td>1.689.741</td>
                        <td>4</td><td>1.689.741</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Riyan Nurdiansyah</td>
                        <td>1</td><td>29.487</td>
                        <td>2</td><td>704.805</td>
                        <td>3</td><td>734.292</td>
                    </tr>
                    <tr style="background-color: #D4E6F1; font-weight: bold;">
                        <td class="text-start">- PT. Infomedia Nusantara</td>
                        <td>7</td><td style="color: red;">878.493</td>
                        <td>2</td><td style="color: red;">987.900</td>
                        <td>9</td><td style="color: red;">1.866.393</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Fitria</td>
                        <td>1</td><td>1.110</td>
                        <td></td><td></td>
                        <td>1</td><td>1.110</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Pipin Fitriani</td>
                        <td>2</td><td>449.875</td>
                        <td>1</td><td>593.850</td>
                        <td>3</td><td>1.043.725</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Ayu Nofikasari Zulkifli</td>
                        <td>1</td><td>350.267</td>
                        <td></td><td></td>
                        <td>1</td><td>350.267</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Kholikah</td>
                        <td>3</td><td>77.241</td>
                        <td></td><td></td>
                        <td>3</td><td>77.241</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">Sandi Syarif Hidayatulloh</td>
                        <td></td><td></td>
                        <td>1</td><td>394.050</td>
                        <td>1</td><td>394.050</td>
                    </tr>
                    <tr style="background-color: #D4E6F1; font-weight: bold;">
                        <td class="text-start">- PT. Kharisma Ide Telekomunikasi</td>
                        <td>1</td><td style="color: red;">472.646</td>
                        <td>2</td><td style="color: red;">962.039</td>
                        <td>3</td><td style="color: red;">1.434.685</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">JAJAT SUDRATAT</td>
                        <td></td><td></td>
                        <td>2</td><td>962.039</td>
                        <td>2</td><td>962.039</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">REZA PRATAMA NUGRAHA</td>
                        <td>1</td><td>472.646</td>
                        <td></td><td></td>
                        <td>1</td><td>472.646</td>
                    </tr>
                    <tr style="background-color: #D4E6F1; font-weight: bold;">
                        <td class="text-start">- M Others</td>
                        <td>2</td><td style="color: red;">649.950</td>
                        <td>2</td><td style="color: red;">1.295.759</td>
                        <td>4</td><td style="color: red;">1.945.709</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-4">M Others</td>
                        <td>2</td><td>649.950</td>
                        <td>2</td><td>1.295.759</td>
                        <td>4</td><td>1.945.709</td>
                    </tr>
                </tbody>
                <tfoot style="background-color: #176B87; color: white; font-weight: bold; border-color: #000;">
                    <tr>
                        <td class="text-start">Grand Total</td>
                        <td>11</td><td>2.030.576</td>
                        <td>18</td><td>7.286.736</td>
                        <td>29</td><td>9.317.312</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tabel 2: BILLING KE - 1 -->
    <div class="col-12 mb-5">
        <div class="table-responsive">
            <h6 class="text-center fw-bold mb-3" style="font-size: 13px;">BILLING KE - 1<br>AGENCY WITEL PRIANGAN TIMUR</h6>
            <table class="table table-bordered align-middle text-center" style="font-size: 12px; white-space: nowrap; border-color: #000;">
                <thead style="background-color: #176B87; color: white; border-color: #000;">
                    <tr>
                        <th class="py-2">AGENCY</th>
                        <th class="py-2">91407 - Inner - Priangan Timur</th>
                        <th class="py-2">91406 - Majalengka</th>
                        <th class="py-2">91404 - Indramayu</th>
                        <th class="py-2">91408 - Singaparna</th>
                        <th class="py-2">Grand Total</th>
                    </tr>
                </thead>
                <tbody style="border-color: #000;">
                    <tr>
                        <td class="text-start">PT. Infomedia Nusantara</td>
                        <td>2</td><td>2</td><td>3</td><td></td><td>7</td>
                    </tr>
                    <tr>
                        <td class="text-start">PT. Panthera Mega Cipta</td>
                        <td></td><td></td><td></td><td>1</td><td>1</td>
                    </tr>
                    <tr>
                        <td class="text-start">PT. Kharisma Ide Telekomunikasi</td>
                        <td></td><td></td><td></td><td>1</td><td>1</td>
                    </tr>
                    <tr>
                        <td class="text-start">M Others</td>
                        <td></td><td></td><td>2</td><td></td><td>2</td>
                    </tr>
                </tbody>
                <tfoot style="background-color: #176B87; color: white; font-weight: bold; border-color: #000;">
                    <tr>
                        <td class="text-center">Grand Total</td>
                        <td>2</td><td>2</td><td>5</td><td>2</td><td>11</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tabel 3: BILLING KE - 2 -->
    <div class="col-12 mb-5">
        <div class="table-responsive">
            <h6 class="text-center fw-bold mb-3" style="font-size: 13px;">BILLING KE - 2<br>AGENCY WITEL PRIANGAN TIMUR</h6>
            <table class="table table-bordered align-middle text-center" style="font-size: 12px; white-space: nowrap; border-color: #000;">
                <thead style="background-color: #176B87; color: white; border-color: #000;">
                    <tr>
                        <th class="py-2">AGENCY</th>
                        <th class="py-2">91403 - Garut</th>
                        <th class="py-2">91406 - Majalengka</th>
                        <th class="py-2">91404 - Indramayu</th>
                        <th class="py-2">91408 - Singaparna</th>
                        <th class="py-2">91409 - Tasikmalaya</th>
                        <th class="py-2">91401 - Banjar</th>
                        <th class="py-2">Grand Total</th>
                    </tr>
                </thead>
                <tbody style="border-color: #000;">
                    <tr>
                        <td class="text-start">PT. Infomedia Nusantara</td>
                        <td></td><td>1</td><td></td><td></td><td></td><td>1</td><td>2</td>
                    </tr>
                    <tr>
                        <td class="text-start">PT. Panthera Mega Cipta</td>
                        <td>10</td><td></td><td></td><td></td><td>2</td><td></td><td>12</td>
                    </tr>
                    <tr>
                        <td class="text-start">PT. Kharisma Ide Telekomunikasi</td>
                        <td></td><td></td><td></td><td>2</td><td></td><td></td><td>2</td>
                    </tr>
                    <tr>
                        <td class="text-start">M Others</td>
                        <td></td><td></td><td>1</td><td>1</td><td></td><td></td><td>2</td>
                    </tr>
                </tbody>
                <tfoot style="background-color: #176B87; color: white; font-weight: bold; border-color: #000;">
                    <tr>
                        <td class="text-center">Grand Total</td>
                        <td>10</td><td>1</td><td>1</td><td>3</td><td>2</td><td>1</td><td>18</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection
