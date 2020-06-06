<?php foreach($exchanges as $exchange):  ?>
<tr>
    <td><img style="max-width: 19.5px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABiCAYAAACvUNYzAAADD0lEQVR4Xu3dMXbUMBAG4JEaSlpKjmCs3Z4bEG4QOo7BMejIDQg3oN9ns0egpE2ZxuKZ7L44u3ayNpY0/8xsG60t/58l5T2PvI4Sf7bbbdV13a33/mq32+0Tnw7u8C5ljw/h/ySi10R0571/bwhPE08KEEK4IqLvg1MawskdnxSgP1dd19fOuW+GMD7XJAcwhOcn+SwAhjCNkA3AEApOQcNT25qQ8b+gqYFnCI/JZJ2CbCSc35LFAGxNeMAoCmAIDAC0IxQfAcdZUevCzAZA60hgBaARgR2ANgSWAJoQ2AJoQWANoAGBPYB0BAgAyQgwAFIRoAAkIsABSEOABJCEAAsgBQEaQAICPAA6gggAZAS32Wy+TJcNYf0lxlgR0YdBr+9jjDdt237meiUuhBC5dm6tfnnv33GtylYBQEQfm6a5XQt0zeOImoK6rnvjnLsmoleDkH40TdOXyV/8ybmpRMwifLIZ5F/YMcZPbdveXJw8EeXeVCICIFH4R7ek0xc8QMrwl4ygOaOtbwsNgB4+NICE8GEBpIQPCSApfDgAaeFDAUgMHwZAavgQAJLDZw8gPXzWABrCZwugJXyWAJrCZwegLXxWABrDZwOgNXwWAJrDLw6gPfyiABb+w7OzIk/ELPzHB5fZASz8p0+NswJY+OeP7LMBWPjj9RJZACz86WKV5AAW/vOVQkkBLPyXy7SSApy+O3pJpdlaiC9HUaZFUoD+ko5vwloSfl3XX8eqnZ1zYl6DnxygR6iq6u1+v/895x473Pm/5nwHsW0WgCXBjLz6fslh2H+HLUCfXAih39VytufLe/+HfbIXdpA1wHANGVyPqB+BYA8gHQECQDICDIBUBCgAiQhwANIQIAEkIcACSEGABpCAAA+AjiACABlBDAAqgigARARxAGgIIgGQEMQCoCCIBkBAEA/AHUEFAGcENQBcEVQBcERQB8ANQSUAJwS1AFwQVANwQFAPUBrBAA4ld6V+z9gABjWPJRAM4KSIdgTB3h19YaHxas3+Z1PJ3E7YCJhIbMmmkrnh9+3/ApVRKwcNOc/dAAAAAElFTkSuQmCC" /></td>
    <td colspan="3">
        <?php echo $exchange['title'].' '.__('by','openpos').' <abbr class="refund_by" title="ID: '.$exchange['user_id'].'">'.$exchange['by'].'</abbr>'; ?>
        <p class="description"><?php $exchange['reason']; ?></p>
        <?php if(count($exchange['exchange_items']) > 0): ?>
        <div class="exchange-items">
            <table border="0">
                <?php foreach($exchange['exchange_items'] as $item): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><small class="times">×</small> <?php echo 1 * $item['qty']; ?></td>
                    <td><?php echo wc_price($item['total']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </td>
    <td class="line_cost"><div class="view"><?php echo wc_price($exchange['addition_amount']); ?></div></td>

    <td></td>
</tr>
<?php endforeach; ?>