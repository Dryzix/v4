test<br />
{*{tpl}tpl2.tpl{/tpl}*}
<br />
{tpl}tpl2.tpl{/tpl}
<br />
Rand : <br />
{rand_b}0|100{/rand_b}
{rand}0|100{/rand}
{rand}0|100{/rand}
<br />
{*{rand}0|100{/rand}*}
Rand test :
{rand_test}
    {rand_max}0{/rand_max}
    {rand_min}10{/rand_min}
{/rand_test}
{rand_test /}
<br />
test