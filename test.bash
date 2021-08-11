for X in ./samples/*.json; do
    cat $X | php run.php > $X.out
done
