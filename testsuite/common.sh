expect_equal() {
  local ret=0
  local output
  output="$(eval $1)" || ret=$?
  if [ "${ret}" != '0' ]; then
    echo "FAIL: $1 ($output) failed with exit code ${ret}"
    return 1
  fi
  if [ "${output}" != "$2" ]; then
    local expected_out=$(mktemp)
    local out=$(mktemp)
    echo "${2}" > "${expected_out}"
    echo "${output}" > "${out}"
    echo "FAIL: $1"
    diff  --label Expected --label Actual -u "$expected_out" "$out"
    rm -f "$expected_out" "$out"
    return 1
  fi
}

_pkgtools() {
  local ret=0
  php -d "include_path=${PWD}/share/php" "${PWD}/bin/pkgtools" "$@" || ret=$?
  return $ret
}
