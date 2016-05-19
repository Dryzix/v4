{{start=1}}
{{start*=2}}
{{start-=2}}
{{limit={{start}}}}
{{limit.=',2'}}
{dbSelect in="req" t=['eleve'] l={{limit}}/}
<ul class="pagination">
    {dbPages for="req" model="default" current='1' limit='8' route='Home#index'}
        {<li><a href="route_first">page_first</a></li>}
        {<li><a href="route_prev">page_prev</a></li>}
        {<li class="active"><a href="#">page_current</a></li>}
        {<li><a href="route_next">page_next</a></li>}
        {<li><a href="route_last">page_last</a></li>}
    {/dbPages}
</ul>