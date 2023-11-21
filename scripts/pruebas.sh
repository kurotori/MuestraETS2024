#!/bin/bash
titulo="Sistema de Torneo de VideoJuegos"
version="1"

respaldoHOME="$HOME"
HOME="../Retroarch/RetroArch-Linux-x86_64.AppImage.home"

retroarch="../Retroarch/RetroArch-Linux-x86_64.AppImage -f --verbose -r a.mkv --recordconfig ./streamConfig.cfg --log-file=$log_file -L "
cores="../Retroarch/RetroArch-Linux-x86_64.AppImage.home/.config/retroarch/cores/"
atari="stella2023_libretro.so "
nes="nestopia_libretro.so "
galaga="../ROMs/galaga.nes"
iceclimber="../ROMs/iceclimber.nes"
megamania="../ROMs/megamania.bin"
dragonfire="../ROMs/dragonfire.bin"

$retroarch$cores$atari$dragonfire

HOME="$respaldoHOME"