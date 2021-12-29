<div class="theme-list-sec">
    <div class="row">
        {foreach item=theme from=$themes}
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="emply-list box">
                    <div class="theme-list-thumb">
                        <a href="#" title="{$theme.name}"><img loading="lazy"
                                src="{$theme.image}"
                                alt="{$theme.name}"></a>
                    </div>
                    <div class="theme-list-info">
                        <h3>{$theme.name}</h3>
                        {if $productId eq null}
                            <a href="manaAffiliateSelectProduct.php?theme={$theme.theme_id}" title="" class="btn btn-action" >{$dict.Select}</a>
                        {else}
                            <a href="manaAffiliateSelectProduct.php?theme={$theme.theme_id}&pid={$productId}" title="" class="btn btn-action" >{$dict.Select}</a>
                        {/if}
                        <h6><a href="https://{$theme.preview}" target="_blank" title="preview"
                                class="btn">{$dict.Preview}</a></h6>
                        <div class="theme-pstn" style="width: 266px;">{'|'|implode:$theme.details}</div>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>

    <div class="col-lg-12">
        {for $i=1 to $numOfPages}
            {if $i eq $currentPage}
                <div id="mana-pagination-container" class="pagination">
                    <a class="btn btn-action" href="?page={$i}&category={$category}">{$i}</a>
                </div>
            {else}
                <div id="mana-pagination-container" class="pagination">
                    <a class="btn" href="?page={$i}&category={$category}">{$i}</a>
                </div>
            {/if}
        {/for}
    </div>
</div>