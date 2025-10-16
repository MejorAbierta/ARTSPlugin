{function name="showArray" data=[]}
    {if $data|@count > 0}
        <ul>
        {foreach $data as $row}
            <li>
                {if $row|@is_array}
                    {call name="showArray" data=$row}
                {else}
                    {$row}
                {/if}
            </li>
        {/foreach}
        </ul>
    {else}
        <em>No data</em>
    {/if}
{/function}