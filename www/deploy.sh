# Authored by Vaent [github.com/Vaent] 2026

# If deployment folder doesn't exist, print warning and exit.
if [[ ! -d ../www-deploy ]]
then
  echo "Deployment files not found - aborting."
  exit 1
fi

# Declare associative array into which versioned file details will be stored.
declare -A versions

# Delete existing deployment files which no longer exist in source.
rgx_vfile="^(.+)\.v([0-9]+)\.([^.]+)$"
for f in $(find ../www-deploy -type f -printf "%P\n")
do
  if [[ "$f" =~ $rgx_vfile ]]
  then
#    echo "Versioned file $f"
    name=${BASH_REMATCH[1]}
    ver=${BASH_REMATCH[2]}
    ext=${BASH_REMATCH[3]}
    unver="$name.$ext"
    if [[ ! -f "$unver" ]]
    then
      echo "Deleting $f from deployment (unversioned file $unver not found in source)"
      rm ../www-deploy/"$f"
    else
      if [[ -f ../www-deploy/"$unver" ]]
      then
        echo "Duplicate versions detected for $unver - aborting."
        if [[ ${#versions[@]} > 0 ]]
        then
          echo "List follows of other versioned files encountered."
          for vf in "${!versions[@]}"; do echo "$vf: ${versions[$vf]}"; done
        fi
        exit 1
      fi

      # Copy the file omitting version number, for comparison with source in later step.
      cp ../www-deploy/"$f" ../www-deploy/"$unver"

      # Find any files which have references to this file.
      # Only CSS and JS should be versioned; only PHP and HTML should contain references.
      for filetoscan in $(find ../www-deploy -iname "*.php" -o -iname "*.html")
      do
        # Replace any references to this file with the unversioned filename.
        sed -i "s~$f~$unver~w tmp_sed" "$filetoscan"
        if [[ -s tmp_sed ]]; then echo "Replaced $f with $unver in $filetoscan"; fi
        rm tmp_sed
      done

      # Add details to versions table.
      versions["$unver"]=$ver
    fi
  else
#    echo "Unversioned file $f"
    if [[ ! -f "$f" ]]
    then
      echo "Deleting $f from deployment (not found in source)"
      rm ../www-deploy/"$f"
    fi
  fi
done

# Similarly delete directories which don't exist in source.
for d in $(find ../www-deploy -depth -type d -printf "%P\n")
do
  if [[ ! -d "$d" ]]
  then
    echo "Removing $d from deployment (not found in source)"
    rmdir ../www-deploy/"$d"
  fi
done

echo "---"
declare -A updated_files

# Check every directory and file in source.
# TODO: skip contents of new directories
for s in $(find . -type d,f -printf "%P\n")
do
  # Skip files/directories which are not part of the website (or are managed separately).
  case "$s" in (deploy.sh|gallery/images*) continue;; esac

  # Copy the file/directory if it doesn't exist in deployment or if the file contents differ.
  if [[ ! -d ../www-deploy/"$s" && ! -f ../www-deploy/"$s" ]] || ([[ -f "$s" ]] && ! cmp -s "$s" ../www-deploy/"$s")
  then
    echo "Updating $s from source"
    cp -r "$s" ../www-deploy/"$s"
    updated_files["../www-deploy/$s"]=""
  fi
done

# TODO: handle new CSS/JS files (which need to be made .v1)

echo "---"
rgx_unver="^(.+)\.([^.]+)$"
for vf in "${!versions[@]}"
do
  oldver=${versions[$vf]}
#  echo "$vf -- $oldver"
  if [[ ! "$vf" =~ $rgx_unver ]]
  then
    echo "Could not parse $vf - aborting due to invalid state."
    echo "List follows of all recorded versioned files."
    for vf in "${!versions[@]}"; do echo "$vf: ${versions[$vf]}"; done
    exit 1
  fi

  name=${BASH_REMATCH[1]}
  ext=${BASH_REMATCH[2]}
  oldfile="$name.v$oldver.$ext"
  if cmp -s ../www-deploy/"$vf" ../www-deploy/"$oldfile"
  then
    # File unchanged - delete unversioned copy, keep old version.
    rm ../www-deploy/"$vf"
    newfile="$oldfile"
    is_new_version=false
  else
    newver=$(($oldver+1))
    newfile="$name.v$newver.$ext"
    echo "Writing $newfile to replace $oldfile"
    mv ../www-deploy/"$vf" ../www-deploy/"$newfile"
    rm ../www-deploy/"$oldfile"
    unset 'updated_files[../www-deploy/$vf]'
    updated_files["../www-deploy/$newfile"]=" (replaced ../www-deploy/$oldfile)"
    is_new_version=true
  fi

  # Update content of any files which reference this file.
  # Only CSS and JS should be versioned; only PHP and HTML should contain references.
  # Any existing references were de-versioned before comparing with source, and must be restored even if unchanged.
  for filetoscan in $(find ../www-deploy -iname "*.php" -o -iname "*.html")
  do
    # Replace any unversioned references to this file with the versioned filename.
    sed -i "s~$vf~$newfile~w tmp_sed" "$filetoscan"
    if [[ -s tmp_sed ]]; then echo "Replaced $vf with $newfile in $filetoscan"; fi
    rm tmp_sed
    if $is_new_version; then updated_files["$filetoscan"]=""; fi
  done
done

echo "---"
echo "The following files have changed:"
for uf in "${!updated_files[@]}"; do echo "$uf${updated_files[$uf]}"; done

echo "---"
echo "Deployment script completed successfully."
