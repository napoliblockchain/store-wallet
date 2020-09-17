#!/bin/bash
# tmux ecc ecc.

echo Starting tmux session...
tmux new-session -d -s "watchtower" protected/yiic watchtower
echo Ready!
