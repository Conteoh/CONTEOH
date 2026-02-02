<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard v3</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard v3</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title"><i class="bi bi-clock"></i> <?= date('Y-m-d H:i:s') ?></h3>
                            </div>
                        </div>
                        <div class="card-body">

                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="bi bi-bullseye"></i> Jogging Target & Progress (<?= (int)($current_year ?? date('Y')) ?>)</h3>
                            <a href="<?= base_url(BACKEND_PORTAL . '/jogging/add') ?>" class="btn btn-sm btn-primary ms-auto">Add Progress</a>
                        </div>
                        <div class="card-body">
                            <?php
                            $jogging_target = $jogging_target ?? null;
                            $jogging_monthly_totals = $jogging_monthly_totals ?? [];
                            $month_names = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            ?>
                            <?php if (empty($jogging_target)): ?>
                                <p class="text-muted mb-0">No jogging target set for this year. <a href="<?= base_url(BACKEND_PORTAL . '/jogging_target/add') ?>">Add a Jogging Target</a>.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0 text-center">
                                        <thead class="">
                                            <tr>
                                                <th>Month</th>
                                                <th class="">Target (KM)</th>
                                                <th class="">Actual (KM)</th>
                                                <th class="">Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for ($m = 1; $m <= 12; $m++):
                                                $key = 'month_' . str_pad((string)$m, 2, '0', STR_PAD_LEFT);
                                                $target_km = isset($jogging_target[$key]) ? (float)$jogging_target[$key] : 0;
                                                $actual_km = isset($jogging_monthly_totals[$m]) ? (float)$jogging_monthly_totals[$m] : 0;
                                                $pct = $target_km > 0 ? min(100, round($actual_km / $target_km * 100, 1)) : ($actual_km > 0 ? 100 : 0);
                                            ?>
                                            <tr>
                                                <td><?= $month_names[(string)$m] ?? $m ?></td>
                                                <td class=""><?= number_format($target_km, 2) ?></td>
                                                <td class=""><?= number_format($actual_km, 2) ?></td>
                                                <td class="">
                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                        <div class="progress flex-grow-1" style="max-width: 80px; height: 20px;">
                                                            <div class="progress-bar <?= $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-info' : 'bg-warning') ?>" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span><?= number_format($pct, 1) ?>%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endfor; ?>
                                        </tbody>
                                        <tfoot class="">
                                            <?php
                                            $total_target = 0;
                                            $total_actual = 0;
                                            for ($m = 1; $m <= 12; $m++) {
                                                $key = 'month_' . str_pad((string)$m, 2, '0', STR_PAD_LEFT);
                                                $total_target += isset($jogging_target[$key]) ? (float)$jogging_target[$key] : 0;
                                                $total_actual += isset($jogging_monthly_totals[$m]) ? (float)$jogging_monthly_totals[$m] : 0;
                                            }
                                            $total_pct = $total_target > 0 ? min(100, round($total_actual / $total_target * 100, 1)) : 0;
                                            ?>
                                            <tr class="fw-bold">
                                                <td>Year Total</td>
                                                <td class=""><?= number_format($total_target, 2) ?></td>
                                                <td class=""><?= number_format($total_actual, 2) ?></td>
                                                <td class=""><?= number_format($total_pct, 1) ?>%</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->