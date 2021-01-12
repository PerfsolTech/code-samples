import React from 'react'
import { useSelector } from 'react-redux'
import styled from 'styled-components'

import SongsListItem from '../SongsListItem/SongsListItem'

const StyledList = styled.ul``

export default function SongsList(props) {
  const songs = useSelector((state) => state.songs)

  return (
    <StyledList>
      {songs &&
        songs.map((song) => <SongsListItem key={song.id} song={song} />)}
    </StyledList>
  )
}
