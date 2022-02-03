<section class="dashboard-counts">
    <div class="col-lg-12">
        <div class="row bg-white has-shadow">
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="icon bg-violet"><i class="icon-user"></i></div>
                    <div class="title"><span>Водителей</span>
                        <div class="progress">
                            <div role="progressbar" style="width: 25%; height: 4px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-violet"></div>
                        </div>
                    </div>
                    <div class="number"><strong>{{ $count_drivers }}</strong></div>
                </div>
            </div>
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="icon bg-red"><i class="icon-padnote"></i></div>
                    <div class="title"><span>Осмотров</span>
                        <div class="progress">
                            <div role="progressbar" style="width: 70%; height: 4px;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-red"></div>
                        </div>
                    </div>
                    <div class="number"><strong>{{ $ankets->count() }}</strong></div>
                </div>
            </div>
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="icon bg-green"><i class="icon-bill"></i></div>
                    <div class="title"><span>Компаний</span>
                        <div class="progress">
                            <div role="progressbar" style="width: 40%; height: 4px;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-green"></div>
                        </div>
                    </div>
                    <div class="number"><strong>{{ $count_companys }}</strong></div>
                </div>
            </div>
            <!-- Item -->
            <div class="col-xl-3 col-sm-6">
                <div class="item d-flex align-items-center">
                    <div class="icon bg-orange"><i class="icon-check"></i></div>
                    <div class="title"><span>Автомобилей</span>
                        <div class="progress">
                            <div role="progressbar" style="width: 50%; height: 4px;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-orange"></div>
                        </div>
                    </div>
                    <div class="number"><strong>{{ $count_cars }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</section>
