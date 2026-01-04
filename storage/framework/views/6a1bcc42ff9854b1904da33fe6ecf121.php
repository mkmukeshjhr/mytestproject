<?php $__env->startSection('subhead'); ?>
    <title>Customers</title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('subcontent'); ?>
    <?php
        use Illuminate\Support\Facades\Auth;
        $authuser=Auth::guard('web')->user();
    ?>
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Admin Earnings</h2>
    <?php if($totalRecords > 0): ?>
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>
    <?php endif; ?>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="<?php echo e(route('adminEarnings')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="<?php echo e($searchString); ?>" type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchString" name="searchString">
                        <?php if(!$searchString): ?>
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        <?php else: ?>
                            <a href="<?php echo e(route('adminEarnings')); ?>"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="x"></i></a>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>

            <div class="w-50 sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="<?php echo e(route('adminEarnings')); ?>" method="GET" id="dropdownForm">
                <select name="orderType" id="orderType" class="form-control  box mr-2 ml-5">
                    <option value="">Order Type</option>
                    <option value="chat" <?php echo e(request('orderType') == 'chat' ? 'selected' : ''); ?>>Chat</option>
                    <option value="call" <?php echo e(request('orderType') == 'call' ? 'selected' : ''); ?>>Call</option>
                  
                    <option value="report" <?php echo e(request('orderType') == 'report' ? 'selected' : ''); ?>>Report</option>
                    <option value="gift" <?php echo e(request('orderType') == 'gift' ? 'selected' : ''); ?>>Gift</option>
                    <option value="gift" <?php echo e(request('orderType') == 'withdraw' ? 'selected' : ''); ?>>Withdraw Request</option>
                </select>
            </form>
        </div>

            <!-- Separate Date Range Filter Form -->
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="<?php echo e(route('adminEarnings')); ?>" method="GET" enctype="multipart/form-data" id="filterForm">
                    <!-- From Date -->
                    <label for="from_date" class="font-bold">From :</label>
                    <input type="date" name="from_date" value="<?php echo e($from_date ?? ''); ?>" class="form-control w-56 box mr-2">

                    <!-- To Date -->
                    <label for="to_date" class="font-bold">To :</label>
                    <input type="date" name="to_date" value="<?php echo e($to_date ?? ''); ?>" class="form-control w-56 box mr-2">

                    <button class="btn btn-primary shadow-md mr-2">Filter</button>
                    <button type="button" id="clearButton" class="btn btn-secondary">
                        <i data-lucide="x"  class="w-4 h-4 mr-1"></i> Clear
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php if($totalRecords > 0): ?>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2" aria-label="customer-list">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">User</th>
                        <th class="whitespace-nowrap"><?php echo e(ucfirst($professionTitle)); ?></th>
                        <th class="text-center whitespace-nowrap">Order Type</th>
                        <th class="text-center whitespace-nowrap">Duration</th>
                        <th class="text-center whitespace-nowrap">Total Amount</th>
                        <th class="text-center whitespace-nowrap"><?php echo e(ucfirst($professionTitle)); ?> Earning</th>
                        <th class="text-center whitespace-nowrap">Admin Earning</th>
                        <th class="text-center whitespace-nowrap">Date</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    <?php
                        $no = 0;
                    ?>
                    <?php $__currentLoopData = $earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="intro-x">
                            <td><?php echo e(($page - 1) * 15 + ++$no); ?></td>
                            <td>
                                <div class="flex items-center">
                                    <div class="image-fit zoom-in" style="height:2.3rem;width:2.3rem;">
                                        <img class="rounded-full cursor-pointer" src="<?php echo e(Str::startsWith($earning->userProfile, ['http://','https://']) ? $earning->userProfile : '/' . $earning->userProfile); ?>" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('<?php echo e($earning->userProfile); ?>')" />
                                    </div>
                                    <div class="ml-4">
                                        <!-- <div class="font-medium"><?php echo e($earning->user ? $earning->user->name : 'N/A'); ?></div> -->
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div class="image-fit zoom-in" style="height:2.3rem;width:2.3rem;">
                                        <img class="rounded-full cursor-pointer" src="<?php echo e(Str::startsWith($earning->astrologerProfile, ['http://','https://']) ? $earning->astrologerProfile : '/' . $earning->astrologerProfile); ?>" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('<?php echo e($earning->astrologerProfile); ?>')" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium"><?php echo e($earning->astrologerName); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap text-center"><?php echo e($earning->orderType ? $earning->orderType : '--'); ?></div>
                            </td>

                            <td>
                                <div class="font-medium whitespace-nowrap text-center"><?php echo e($earning->totalMin ? $earning->totalMin.' min' : '--'); ?></div>
                            </td>

                            <?php if($authuser->country=='India'): ?>
                                <?php
                                    $earning->totalPayable=convertusdtoinr( $earning->totalPayable,$earning->inr_usd_conversion_rate?:1);
                                    $earning->adminearningAmount=convertusdtoinr(  $earning->adminearningAmount,$earning->inr_usd_conversion_rate?:1);
                                ?>
                            <?php endif; ?>
                            <td>
                                <div class="font-medium whitespace-nowrap text-center"><?php echo e($currency->value); ?><?php echo e($earning->totalPayable ? number_format($earning->totalPayable,2) : '--'); ?></div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap text-center"><?php echo e($currency->value); ?><?php echo e(number_format($earning->totalPayable-$earning->adminearningAmount,2) ?? '--'); ?></div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap text-center"><?php echo e($currency->value); ?><?php echo e($earning->adminearningAmount ? number_format($earning->adminearningAmount,2) : '--'); ?></div>
                            </td>

                            <td class="text-center">
                                <?php echo e(date('d-m-Y h:i a', strtotime($earning->updated_at)) ? date('d-m-Y h:i a', strtotime($earning->updated_at)) : '--'); ?>

                            </td>


                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <!-- Fullscreen Image Viewer -->
         <div class="image-overlay" id="imageOverlay">
                <img src="your-image.jpg" id="popupImage" alt="Full Screen Image">
                <span class="closebtn" id="closeBtn">&times;</span>
            </div>
            <script>
            const overlay = document.getElementById('imageOverlay');
            const closeBtn = document.getElementById('closeBtn');
            function openImage(src) {
                document.getElementById('popupImage').src = src;
                overlay.classList.add('active');
            }
            closeBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
            });
            overlay.addEventListener('click', (e) => {
                if(e.target === overlay) {
                    overlay.classList.remove('active');
                }
            });
            </script>
        <!-- END: Data List -->
        <!-- BEGIN: Pagination -->
        <?php if($totalRecords > 0): ?>
        <div class="d-inline text-slate-500 pagecount">Showing <?php echo e($start); ?> to <?php echo e($end); ?> of <?php echo e($totalRecords); ?> entries</div>
    <?php endif; ?>
    <div class="d-inline intro-y col-span-12 addbtn">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination" id="pagination">
                <li class="page-item <?php echo e($page == 1 ? 'disabled' : ''); ?>">
                    <a class="page-link"
                        href="<?php echo e(route('adminEarnings', ['page' => $page - 1, 'searchString' => $searchString])); ?>">
                        <i class="w-4 h-4" data-lucide="chevron-left"></i>
                    </a>
                </li>

                <?php
                    $showPages = 15; // Number of pages to show at a time
                    $halfShowPages = floor($showPages / 2);
                    $startPage = max(1, $page - $halfShowPages);
                    $endPage = min($startPage + $showPages - 1, $totalPages);
                ?>

                <?php if($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="<?php echo e(route('adminEarnings', ['page' => 1, 'searchString' => $searchString])); ?>">1</a>
                    </li>
                    <?php if($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo e($page == $i ? 'active' : ''); ?>">
                        <a class="page-link"
                            href="<?php echo e(route('adminEarnings', ['page' => $i, 'searchString' => $searchString])); ?>"><?php echo e($i); ?></a>
                    </li>
                <?php endfor; ?>

                <?php if($endPage < $totalPages): ?>
                    <?php if($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link"
                            href="<?php echo e(route('adminEarnings', ['page' => $totalPages, 'searchString' => $searchString])); ?>"><?php echo e($totalPages); ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?php echo e($page == $totalPages ? 'disabled' : ''); ?>">
                    <a class="page-link"
                        href="<?php echo e(route('adminEarnings', ['page' => $page + 1, 'searchString' => $searchString])); ?>">
                        <i class="w-4 h-4" data-lucide="chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <?php else: ?>
        <div class="intro-y" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- END: Pagination -->


<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  ></script>
<script>
    $(document).ready(function() {
        jQuery('.select2').select2();
    });
</script>
<script>

function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }


        function numbersOnly(e) {
            var keycode = e.keyCode;
            if (!(keycode != 9 && e.shiftKey == false && (keycode == 46 || keycode == 8 || keycode ==
                    37 ||
                    keycode == 39 || (keycode >=
                        48 && keycode <= 57)))) {
                e.preventDefault();
            }
        }

        document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "<?php echo e(route('adminEarnings')); ?>"; // Redirect to remove query parameters
    });



</script>

    <script type="text/javascript">
        var spinner = $('.loader');
        jQuery(function() {
            jQuery('.printpdf').click(function(e) {
                e.preventDefault();
                spinner.show();
                var searchString = $("#searchString").val();
                jQuery.ajax({
                    type: 'GET',
                    url: "<?php echo e(route('printAdminEarnings')); ?>",
                    data: {
                        "searchString": searchString,
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "adminearning.pdf";
                            link.click();
                            spinner.hide();
                            // location.reload();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
            jQuery('.downloadcsv').click(function(e) {
                e.preventDefault();
                spinner.show();
                var searchString = $("#searchString").val();
                jQuery.ajax({
                    type: 'GET',
                    url: "<?php echo e(route('exportAdminEarningCSV')); ?>",
                    data: {
                        "searchString": searchString,
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "adminEarnings.csv";
                            link.click();
                            spinner.hide();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })

    document.getElementById('orderType').addEventListener('change', function() {
        document.getElementById('dropdownForm').submit();

    });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('../layout/' . $layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH G:\xampp\htdocs\astroway\Website\resources\views/pages/admin-earnings.blade.php ENDPATH**/ ?>