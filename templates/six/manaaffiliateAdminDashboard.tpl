<div class="table-container clearfix">
    <div id="tableServicesList_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
        <div class="listtable">
            <div id="tableServicesList_filter" class="dataTables_filter" style="height: 40px;"></div>
            <table id="tableServicesList" class="table table-list dataTable no-footer dtr-inline"
                aria-describedby="tableServicesList_info" role="grid" style="width: 0px;">
                <thead>
                    <tr role="row">
                        <th tabindex="0" aria-controls="tableServicesList" rowspan="1" colspan="1" style="width: 0px;">
                            محصول/سرویس</th>
                        <th tabindex="0" aria-controls="tableServicesList" rowspan="1" colspan="1" style="width: 0px;">
                            قیمت</th>
                        <th tabindex="0" aria-controls="tableServicesList" rowspan="1" colspan="1" style="width: 0px;">
                            تاریخ
                            سررسید</th>
                        <th tabindex="0" aria-controls="tableServicesList" rowspan="1" colspan="1" style="width: 0px;">
                            وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$services item=service}
                    <tr onclick="clickableSafeRedirect(event, '{$WEB_ROOT}/clientarea.php?action=productdetails&amp;id={$service.id}', false)" role="row" class="odd">
                        <td tabindex="0"><strong>{$service.translated_name}</strong></td>
                        <td class="text-center">{$service.recurringamount}<br>{$service.billingcycle}</td>
                        <td class="text-center">{$service.nextduedate}</td>
                        <td class="text-center"><span class="label status">{Lang::trans($service.status)}</span>
                        </td>
                        <td class="responsive-edit-button" style="">
                            <a href="{$WEB_ROOT}/clientarea.php?action=productdetails&amp;id={$service.id}" class="btn btn-block btn-info">
                                مدیریت محصول
                            </a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="dataTables_paginate paging_simple_numbers" id="tableServicesList_paginate">
            <ul class="pagination">
                <li class="paginate_button previous disabled" aria-controls="tableServicesList" tabindex="0"
                    id="tableServicesList_previous"><a href="#">قبلی</a></li>
                <li class="paginate_button active" aria-controls="tableServicesList" tabindex="0"><a href="#">1</a></li>
                <li class="paginate_button " aria-controls="tableServicesList" tabindex="0"><a href="#">2</a></li>
                <li class="paginate_button " aria-controls="tableServicesList" tabindex="0"><a href="#">3</a></li>
                <li class="paginate_button " aria-controls="tableServicesList" tabindex="0"><a href="#">4</a></li>
                <li class="paginate_button next" aria-controls="tableServicesList" tabindex="0"
                    id="tableServicesList_next"><a href="#">بعدی</a></li>
            </ul>
        </div>
        <div class="dataTables_length" id="tableServicesList_length" style="height: 40px;"><label>نمایش مقادیر</label>
        </div>
    </div>
</div>